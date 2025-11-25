<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Server;
use Illuminate\Http\Request;

class ServerController extends Controller
{
    /**
     * Display a listing of servers
     */
    public function index(Request $request)
    {
        $query = Server::query();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        $servers = $query->orderBy('expired_date')->get();

        // Get counts for status badges
        $statusCounts = [
            'all' => Server::count(),
            'active' => Server::where('status', 'active')->count(),
            'expired' => Server::where('status', 'expired')->count(),
            'suspended' => Server::where('status', 'suspended')->count(),
        ];

        return view('admin.servers.index', compact('servers', 'statusCounts'));
    }

    /**
     * Show the form for creating a new server
     */
    public function create()
    {
        return view('admin.servers.create');
    }

    /**
     * Store a newly created server
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'ip_address' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'required|string',
            'login_link' => 'required|url',
            'expired_date' => 'required|date|after:today',
            'status' => 'required|in:active,expired,suspended',
            'notes' => 'nullable|string',
        ]);

        Server::create($validated);

        return redirect()->route('admin.servers.index')
            ->with('success', 'Server berhasil ditambahkan');
    }

    /**
     * Show the form for editing the specified server
     */
    public function edit(Server $server)
    {
        return view('admin.servers.edit', compact('server'));
    }

    /**
     * Update the specified server
     */
    public function update(Request $request, Server $server)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'ip_address' => 'required|string|max:255',
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

        $server->update($validated);

        return redirect()->route('admin.servers.index')
            ->with('success', 'Server berhasil diperbarui');
    }

    /**
     * Remove the specified server
     */
    public function destroy(Server $server)
    {
        // Check if server has clients
        if ($server->clients()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Server tidak dapat dihapus karena masih memiliki client terkait');
        }

        $server->delete();

        return redirect()->route('admin.servers.index')
            ->with('success', 'Server berhasil dihapus');
    }

    /**
     * Toggle server status
     */
    public function toggleStatus(Server $server)
    {
        $newStatus = $server->status === 'active' ? 'suspended' : 'active';
        $server->update(['status' => $newStatus]);

        return response()->json([
            'success' => true,
            'message' => "Status server berhasil diubah menjadi {$newStatus}",
            'new_status' => $newStatus
        ]);
    }

    /**
     * Get server password (decrypted)
     */
    public function getPassword(Server $server)
    {
        return response()->json([
            'success' => true,
            'password' => $server->decrypted_password
        ]);
    }
}
