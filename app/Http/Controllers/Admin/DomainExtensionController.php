<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DomainExtension;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class DomainExtensionController extends Controller
{
    /**
     * Display a listing of domain extensions.
     */
    public function index(Request $request)
    {
        $query = DomainExtension::query();

        // Filter by extension
        if ($request->filled('extension')) {
            $query->byExtension($request->extension);
        }

        // Filter by duration
        if ($request->filled('duration')) {
            $query->byDuration($request->duration);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $domainExtensions = $query->orderBy('extension')->orderBy('duration_years')->get();
        
        // Get unique extensions for filter dropdown
        $extensions = DomainExtension::distinct()->pluck('extension')->sort();
        $durations = DomainExtension::distinct()->pluck('duration_years')->sort();

        return view('admin.domain-extensions.index', compact('domainExtensions', 'extensions', 'durations'));
    }

    /**
     * Show the form for creating a new domain extension.
     */
    public function create()
    {
        return view('admin.domain-extensions.create');
    }

    /**
     * Store a newly created domain extension in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'extension' => 'required|string|max:10',
            'duration_years' => 'required|integer|min:1|max:10',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
            'is_active' => 'boolean'
        ], [
            'extension.required' => 'Extension domain wajib diisi',
            'duration_years.required' => 'Durasi langganan wajib diisi',
            'duration_years.min' => 'Durasi minimal 1 tahun',
            'duration_years.max' => 'Durasi maksimal 10 tahun',
            'price.required' => 'Harga wajib diisi',
            'price.min' => 'Harga tidak boleh negatif'
        ]);

        // Check for unique extension + duration combination
        $existing = DomainExtension::where('extension', $request->extension)
            ->where('duration_years', $request->duration_years)
            ->first();

        if ($existing) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Extension domain dengan durasi ini sudah ada');
        }

        try {
            DomainExtension::create([
                'extension' => $request->extension,
                'duration_years' => $request->duration_years,
                'price' => $request->price,
                'description' => $request->description,
                'is_active' => $request->boolean('is_active', true)
            ]);

            Log::info('Domain extension created', [
                'extension' => $request->extension,
                'duration' => $request->duration_years,
                'price' => $request->price,
                'admin' => auth()->user()->name
            ]);

            return redirect()->route('admin.domain-extensions.index')
                ->with('success', 'Extension domain berhasil ditambahkan');
        } catch (\Exception $e) {
            Log::error('Error creating domain extension', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menambahkan extension domain');
        }
    }

    /**
     * Show the form for editing the specified domain extension.
     */
    public function edit(DomainExtension $domainExtension)
    {
        return view('admin.domain-extensions.edit', compact('domainExtension'));
    }

    /**
     * Update the specified domain extension in storage.
     */
    public function update(Request $request, DomainExtension $domainExtension)
    {
        $request->validate([
            'extension' => [
                'required',
                'string',
                'max:10',
                Rule::unique('domain_extensions')->where(function ($query) use ($request, $domainExtension) {
                    return $query->where('extension', $request->extension)
                        ->where('duration_years', $request->duration_years)
                        ->where('id', '!=', $domainExtension->id);
                })
            ],
            'duration_years' => 'required|integer|min:1|max:10',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
            'is_active' => 'boolean'
        ], [
            'extension.required' => 'Extension domain wajib diisi',
            'extension.unique' => 'Extension domain sudah ada untuk durasi ini',
            'duration_years.required' => 'Durasi langganan wajib diisi',
            'duration_years.min' => 'Durasi minimal 1 tahun',
            'duration_years.max' => 'Durasi maksimal 10 tahun',
            'price.required' => 'Harga wajib diisi',
            'price.min' => 'Harga tidak boleh negatif'
        ]);

        try {
            $domainExtension->update([
                'extension' => $request->extension,
                'duration_years' => $request->duration_years,
                'price' => $request->price,
                'description' => $request->description,
                'is_active' => $request->boolean('is_active', true)
            ]);

            Log::info('Domain extension updated', [
                'id' => $domainExtension->id,
                'extension' => $request->extension,
                'duration' => $request->duration_years,
                'price' => $request->price,
                'admin' => auth()->user()->name
            ]);

            return redirect()->route('admin.domain-extensions.index')
                ->with('success', 'Extension domain berhasil diperbarui');
        } catch (\Exception $e) {
            Log::error('Error updating domain extension', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui extension domain');
        }
    }

    /**
     * Remove the specified domain extension from storage.
     */
    public function destroy(DomainExtension $domainExtension)
    {
        try {
            $domainExtension->delete();

            Log::info('Domain extension deleted', [
                'id' => $domainExtension->id,
                'extension' => $domainExtension->extension,
                'duration' => $domainExtension->duration_years,
                'admin' => auth()->user()->name
            ]);

            return redirect()->route('admin.domain-extensions.index')
                ->with('success', 'Extension domain berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('Error deleting domain extension', [
                'error' => $e->getMessage(),
                'id' => $domainExtension->id
            ]);

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menghapus extension domain');
        }
    }

    /**
     * Bulk delete domain extensions
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:domain_extensions,id'
        ]);

        $count = count($request->ids);
        DomainExtension::whereIn('id', $request->ids)->delete();

        Log::info('Domain extensions bulk deleted', [
            'count' => $count,
            'admin' => auth()->user()->name
        ]);

        return redirect()->route('admin.domain-extensions.index')
            ->with('success', "{$count} extension(s) berhasil dihapus");
    }

    /**
     * Toggle the active status of a domain extension.
     */
    public function toggleStatus(DomainExtension $domainExtension)
    {
        try {
            $domainExtension->update([
                'is_active' => !$domainExtension->is_active
            ]);

            $status = $domainExtension->is_active ? 'diaktifkan' : 'dinonaktifkan';

            Log::info('Domain extension status toggled', [
                'id' => $domainExtension->id,
                'extension' => $domainExtension->extension,
                'duration' => $domainExtension->duration_years,
                'new_status' => $domainExtension->is_active,
                'admin' => auth()->user()->name
            ]);

            return redirect()->back()
                ->with('success', "Extension domain berhasil {$status}");
        } catch (\Exception $e) {
            Log::error('Error toggling domain extension status', [
                'error' => $e->getMessage(),
                'id' => $domainExtension->id
            ]);

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mengubah status extension domain');
        }
    }
}
