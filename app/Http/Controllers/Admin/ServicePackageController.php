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
            ->get();

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
        $package = ServicePackage::with('freeDomains.domainExtension')->findOrFail($id);

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
            'is_active' => 'boolean'
        ]);

        // Process free domains separately with custom validation
        $freeDomains = $request->input('free_domains', []);
        $processedDomains = [];
        
        if (!empty($freeDomains)) {
            foreach ($freeDomains as $index => $domain) {
                // Skip empty domain rows
                if (empty($domain['domain_extension_id'])) {
                    continue;
                }
                
                // Validate required fields for non-empty rows
                if (empty($domain['duration_years']) || !isset($domain['discount_percent'])) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['free_domains' => 'Domain yang dipilih harus memiliki durasi dan diskon.']);
                }
                
                $processedDomains[] = [
                    'service_package_id' => $package->id,
                    'domain_extension_id' => $domain['domain_extension_id'],
                    'duration_years' => $domain['duration_years'],
                    'discount_percent' => $domain['discount_percent'],
                    'is_free' => isset($domain['is_free']) ? 1 : 0,
                    'sort_order' => count($processedDomains),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            
            // Check for duplicates in processed domains
            $domainIds = array_column($processedDomains, 'domain_extension_id');
            if (count($domainIds) !== count(array_unique($domainIds))) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['free_domains' => 'Tidak dapat menambahkan domain yang sama lebih dari satu kali dalam satu paket.']);
            }
        }

        DB::transaction(function () use ($request, $package, $validated, $processedDomains) {
            // Update package basic info
            $package->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'base_price' => $validated['base_price'],
                'is_active' => $request->has('is_active') ? 1 : 0,
            ]);

            // Remove existing free domains
            $package->freeDomains()->delete();

            // Add new free domains if any
            if (!empty($processedDomains)) {
                ServicePackageFreeDomain::insert($processedDomains);
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
     * Bulk delete service packages
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:service_packages,id'
        ]);

        // Check if any packages are being used
        $usedPackages = \DB::table('services')
            ->whereIn('package_id', $request->ids)
            ->distinct()
            ->pluck('package_id')
            ->toArray();

        if (!empty($usedPackages)) {
            return redirect()->route('admin.service-packages.index')
                ->with('error', 'Cannot delete packages that are being used by services');
        }

        $count = count($request->ids);
        ServicePackage::whereIn('id', $request->ids)->delete();

        return redirect()->route('admin.service-packages.index')
            ->with('success', "{$count} package(s) deleted successfully");
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
