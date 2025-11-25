<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DomainRegister;
use Illuminate\Http\Request;

class DomainRegisterController extends Controller
{
    /**
     * Display a listing of domain registers
     */
    public function index(Request $request)
    {
        $query = DomainRegister::query();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        $registers = $query->orderBy('expired_date')->get();

        // Get counts for status badges
        $statusCounts = [
            'all' => DomainRegister::count(),
            'active' => DomainRegister::where('status', 'active')->count(),
            'expired' => DomainRegister::where('status', 'expired')->count(),
            'suspended' => DomainRegister::where('status', 'suspended')->count(),
        ];

        return view('admin.domain-registers.index', compact('registers', 'statusCounts'));
    }

    /**
     * Show the form for creating a new domain register
     */
    public function create()
    {
        return view('admin.domain-registers.create');
    }

    /**
     * Store a newly created domain register
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'required|string',
            'login_link' => 'required|url',
            'expired_date' => 'required|date|after:today',
            'status' => 'required|in:active,expired,suspended',
            'notes' => 'nullable|string',
        ]);

        DomainRegister::create($validated);

        return redirect()->route('admin.domain-registers.index')
            ->with('success', 'Register domain berhasil ditambahkan');
    }

    /**
     * Show the form for editing the specified domain register
     */
    public function edit(DomainRegister $register)
    {
        return view('admin.domain-registers.edit', compact('register'));
    }

    /**
     * Update the specified domain register
     */
    public function update(Request $request, DomainRegister $register)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'nullable|string',
            'login_link' => 'required|url',
            'expired_date' => 'required|date',
            'status' => 'required|in:active,expired,suspended',
            'notes' => 'nullable|string',
        ]);

        // Only update password if provided
        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $register->update($validated);

        return redirect()->route('admin.domain-registers.index')
            ->with('success', 'Register domain berhasil diperbarui');
    }

    /**
     * Remove the specified domain register
     */
    public function destroy(DomainRegister $register)
    {
        // Check if register has clients
        if ($register->clients()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Register domain tidak dapat dihapus karena masih memiliki client terkait');
        }

        $register->delete();

        return redirect()->route('admin.domain-registers.index')
            ->with('success', 'Register domain berhasil dihapus');
    }

    /**
     * Toggle register status
     */
    public function toggleStatus(DomainRegister $register)
    {
        $newStatus = $register->status === 'active' ? 'suspended' : 'active';
        $register->update(['status' => $newStatus]);

        return response()->json([
            'success' => true,
            'message' => "Status register domain berhasil diubah menjadi {$newStatus}",
            'new_status' => $newStatus
        ]);
    }

    /**
     * Get register password (decrypted)
     */
    public function getPassword(DomainRegister $register)
    {
        return response()->json([
            'success' => true,
            'password' => $register->decrypted_password
        ]);
    }
}
