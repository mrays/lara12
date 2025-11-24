<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServicePackage;
use Illuminate\Http\Request;

class ServicePackageController extends Controller
{
    /**
     * Display a listing of service packages
     */
    public function index()
    {
        $packages = \DB::table('service_packages')
            ->select('id', 'name', 'description', 'base_price', 'is_active', 'created_at')
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

        \DB::table('service_packages')->insert([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'base_price' => $validated['base_price'],
            'is_active' => $request->has('is_active') ? 1 : 0,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('admin.service-packages.index')
            ->with('success', 'Service package created successfully!');
    }

    /**
     * Display the specified service package
     */
    public function show($id)
    {
        $package = \DB::table('service_packages')->where('id', $id)->first();
        
        if (!$package) {
            return redirect()->route('admin.service-packages.index')
                ->with('error', 'Service package not found.');
        }

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
        $package = \DB::table('service_packages')->where('id', $id)->first();
        
        if (!$package) {
            return redirect()->route('admin.service-packages.index')
                ->with('error', 'Service package not found.');
        }

        return view('admin.service-packages.edit', compact('package'));
    }

    /**
     * Update the specified service package
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'base_price' => 'required|numeric|min:0',
            'is_active' => 'boolean'
        ]);

        \DB::table('service_packages')
            ->where('id', $id)
            ->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'base_price' => $validated['base_price'],
                'is_active' => $request->has('is_active') ? 1 : 0,
                'updated_at' => now()
            ]);

        return redirect()->route('admin.service-packages.index')
            ->with('success', 'Service package updated successfully!');
    }

    /**
     * Remove the specified service package
     */
    public function destroy($id)
    {
        // Check if package is being used by any services
        $servicesCount = \DB::table('services')->where('package_id', $id)->count();
        
        if ($servicesCount > 0) {
            return redirect()->route('admin.service-packages.index')
                ->with('error', "Cannot delete package. It is being used by {$servicesCount} service(s).");
        }

        \DB::table('service_packages')->where('id', $id)->delete();

        return redirect()->route('admin.service-packages.index')
            ->with('success', 'Service package deleted successfully!');
    }

    /**
     * Toggle package status
     */
    public function toggleStatus($id)
    {
        $package = \DB::table('service_packages')->where('id', $id)->first();
        
        if (!$package) {
            return redirect()->route('admin.service-packages.index')
                ->with('error', 'Service package not found.');
        }

        \DB::table('service_packages')
            ->where('id', $id)
            ->update([
                'is_active' => !$package->is_active,
                'updated_at' => now()
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
        $packages = \DB::table('service_packages')
            ->where('is_active', 1)
            ->select('id', 'name', 'description', 'base_price')
            ->orderBy('name')
            ->get();

        return response()->json($packages);
    }
}
