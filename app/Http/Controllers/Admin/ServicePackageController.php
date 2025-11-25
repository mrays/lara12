<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServicePackage;
use App\Models\DomainExtension;
use App\Models\ServicePackageFreeDomain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServicePackageController extends Controller
{
    /**
     * Display a listing of service packages
     */
    public function index()
    {
        $packages = ServicePackage::select('id', 'name', 'description', 'base_price', 'is_active', 'created_at')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.service-packages.index', compact('packages'));
    }

    /**
     * Show the form for creating a new service package
     */
    public function create()
    {
        return view('admin.service-packages.create');
    }

    /**
     * Store a newly created service package
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'base_price' => 'required|numeric|min:0',
            'is_active' => 'boolean'
        ]);

        ServicePackage::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'base_price' => $validated['base_price'],
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('admin.service-packages.index')
            ->with('success', 'Service package created successfully!');
    }

    /**
     * Display the specified service package
     */
    public function show($id)
    {
        $package = ServicePackage::findOrFail($id);

        // Get services using this package
        $services = \DB::table('services')
            ->leftJoin('users', 'services.client_id', '=', 'users.id')
            ->where('services.package_id', $id)
            ->select('services.*', 'users.name as client_name')
            ->get();

        return view('admin.service-packages.show', compact('package', 'services'));
    }

    /**
     * Show the form for editing the specified service package
     */
    public function edit($id)
    {
        $package = ServicePackage::with('freeDomains.domainExtension')->findOrFail($id);
        $domainExtensions = DomainExtension::active()->orderBy('extension')->orderBy('duration_years')->get();
        
        // Group domain extensions by extension for better organization
        $groupedDomains = $domainExtensions->groupBy('extension');
        
        return view('admin.service-packages.edit', compact('package', 'domainExtensions', 'groupedDomains'));
    }

    /**
     * Update the specified service package
     */
    public function update(Request $request, $id)
    {
        $package = ServicePackage::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'base_price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
            'free_domains' => 'nullable|array',
            'free_domains.*.domain_extension_id' => 'required|exists:domain_extensions,id',
            'free_domains.*.duration_years' => 'required|integer|min:1|max:10',
            'free_domains.*.discount_percent' => 'required|numeric|min:0|max:100',
            'free_domains.*.is_free' => 'boolean'
        ]);

        // Check for duplicate domain extensions within the same package
        if (!empty($validated['free_domains'])) {
            $domainIds = array_column($validated['free_domains'], 'domain_extension_id');
            if (count($domainIds) !== count(array_unique($domainIds))) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['free_domains' => 'Tidak dapat menambahkan domain yang sama lebih dari satu kali dalam satu paket.']);
            }
        }

        DB::transaction(function () use ($request, $package, $validated) {
            // Update package basic info
            $package->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'base_price' => $validated['base_price'],
                'is_active' => $request->has('is_active') ? 1 : 0,
            ]);

            // Remove existing free domains
            $package->freeDomains()->delete();

            // Add new free domains if provided
            if (!empty($validated['free_domains'])) {
                $freeDomains = [];
                foreach ($validated['free_domains'] as $index => $domain) {
                    $freeDomains[] = [
                        'service_package_id' => $package->id,
                        'domain_extension_id' => $domain['domain_extension_id'],
                        'duration_years' => $domain['duration_years'],
                        'discount_percent' => $domain['discount_percent'],
                        'is_free' => isset($domain['is_free']) ? 1 : 0,
                        'sort_order' => $index,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                ServicePackageFreeDomain::insert($freeDomains);
            }
        });

        return redirect()->route('admin.service-packages.index')
            ->with('success', 'Service package updated successfully!');
    }

    /**
     * Remove the specified service package
     */
    public function destroy($id)
    {
        $package = ServicePackage::findOrFail($id);
        
        // Check if package is being used by any services
        $servicesCount = \DB::table('services')->where('package_id', $id)->count();
        
        if ($servicesCount > 0) {
            return redirect()->route('admin.service-packages.index')
                ->with('error', "Cannot delete package. It is being used by {$servicesCount} service(s).");
        }

        $package->delete();

        return redirect()->route('admin.service-packages.index')
            ->with('success', 'Service package deleted successfully!');
    }

    /**
     * Toggle package status
     */
    public function toggleStatus($id)
    {
        $package = ServicePackage::findOrFail($id);
        
        $package->update([
            'is_active' => !$package->is_active,
        ]);

        $status = !$package->is_active ? 'activated' : 'deactivated';
        
        return redirect()->route('admin.service-packages.index')
            ->with('success', "Service package {$status} successfully!");
    }

    /**
     * Get active packages for API/AJAX
     */
    public function getActivePackages()
    {
        $packages = ServicePackage::where('is_active', 1)
            ->select('id', 'name', 'description', 'base_price')
            ->orderBy('name')
            ->get();

        return response()->json($packages);
    }
}
