<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClientData;
use App\Models\Domain;
use App\Models\Server;
use App\Models\DomainRegister;
use App\Models\User;
use Illuminate\Http\Request;

class ClientDataController extends Controller
{
    /**
     * Display a listing of client data
     */
    public function index(Request $request)
    {
        $query = ClientData::with(['server', 'domainRegister', 'user', 'domain']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by server
        if ($request->filled('server_id')) {
            $query->where('server_id', $request->server_id);
        }

        // Filter by domain register
        if ($request->filled('domain_register_id')) {
            $query->where('domain_register_id', $request->domain_register_id);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('whatsapp', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        $clients = $query->orderBy('name')->get();

        // Get counts for status badges using domain expiration
        $allClients = ClientData::with('domain')->get();
        
        $statusCounts = [
            'all' => $allClients->count(),
            'active' => $allClients->where('status', 'active')->count(),
            'expired' => $allClients->where('status', 'expired')->count(),
            'warning' => $allClients->where('status', 'warning')->count(),
        ];

        // Get related data for filters
        $servers = Server::orderBy('name')->get();
        $domainRegisters = DomainRegister::orderBy('name')->get();

        return view('admin.client-data.index', compact('clients', 'statusCounts', 'servers', 'domainRegisters'));
    }

    /**
     * Show the form for creating new client data
     */
    public function create()
    {
        $servers = Server::orderBy('name')->get();
        $domainRegisters = DomainRegister::orderBy('name')->get();
        $domains = Domain::with('client')->orderBy('domain_name')->get();
        $users = User::where('role', 'client')->orderBy('name')->get();

        return view('admin.client-data.create', compact('servers', 'domainRegisters', 'domains', 'users'));
    }

    /**
     * Store newly created client data
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'whatsapp' => 'required|string|max:20',
            'domain_id' => 'required|exists:domains,id',
            'server_id' => 'nullable|exists:servers,id',
            'domain_register_id' => 'nullable|exists:domain_registers,id',
            'user_id' => 'nullable|exists:users,id',
            'status' => 'required|in:active,expired,warning',
            'notes' => 'nullable|string',
        ]);

        ClientData::create($validated);

        return redirect()->route('admin.client-data.index')
            ->with('success', 'Data client berhasil ditambahkan');
    }

    /**
     * Show the form for editing the specified client data
     */
    public function edit(ClientData $client)
    {
        $servers = Server::orderBy('name')->get();
        $domainRegisters = DomainRegister::orderBy('name')->get();
        $domains = Domain::orderBy('domain_name')->get();
        $users = User::where('role', 'client')->orderBy('name')->get();

        return view('admin.client-data.edit', compact('client', 'servers', 'domainRegisters', 'domains', 'users'));
    }

    /**
     * Update the specified client data
     */
    public function update(Request $request, ClientData $client)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'whatsapp' => 'required|string|max:20',
            'domain_id' => 'required|exists:domains,id',
            'server_id' => 'nullable|exists:servers,id',
            'domain_register_id' => 'nullable|exists:domain_registers,id',
            'user_id' => 'nullable|exists:users,id',
            'status' => 'required|in:active,expired,warning',
            'notes' => 'nullable|string',
        ]);

        $client->update($validated);

        return redirect()->route('admin.client-data.index')
            ->with('success', 'Data client berhasil diperbarui');
    }

    /**
     * Remove the specified client data
     */
    public function destroy(ClientData $client)
    {
        $client->delete();

        return redirect()->route('admin.client-data.index')
            ->with('success', 'Data client berhasil dihapus');
    }

    /**
     * Get service status overview
     */
    public function serviceStatus()
    {
        $clients = ClientData::with(['server', 'domainRegister', 'domain'])->get();
        
        $overview = [
            'total_clients' => $clients->count(),
            'expiring_soon' => $clients->filter(fn($c) => $c->isAnyServiceExpiringSoon())->count(),
            'expired_services' => $clients->filter(fn($c) => $c->isAnyServiceExpired())->count(),
            'servers_in_use' => $clients->whereNotNull('server_id')->pluck('server_id')->unique()->count(),
            'registers_in_use' => $clients->whereNotNull('domain_register_id')->pluck('domain_register_id')->unique()->count(),
        ];

        // Group by server with complete data
        $serverStats = $clients->whereNotNull('server_id')
            ->groupBy('server_id')
            ->map(function($group, $serverId) {
                $server = $group->first()->server;
                return [
                    'server_id' => $serverId,
                    'server' => $server,
                    'server_name' => $server->name ?? 'Unknown',
                    'ip_address' => $server->ip_address ?? 'N/A',
                    'status' => $server->status ?? 'unknown',
                    'status_badge_class' => $server->status_badge_class ?? 'bg-secondary',
                    'client_count' => $group->count(),
                    'expiring_soon' => $group->filter(fn($c) => $c->isAnyServiceExpiringSoon())->count(),
                    'expired' => $group->filter(fn($c) => $c->isAnyServiceExpired())->count(),
                ];
            });

        // Group by domain register with complete data
        $registerStats = $clients->whereNotNull('domain_register_id')
            ->groupBy('domain_register_id')
            ->map(function($group, $registerId) {
                $register = $group->first()->domainRegister;
                return [
                    'register_id' => $registerId,
                    'register' => $register,
                    'register_name' => $register->name ?? 'Unknown',
                    'login_link' => $register->login_link ?? '#',
                    'status' => $register->status ?? 'unknown',
                    'status_badge_class' => $register->status_badge_class ?? 'bg-secondary',
                    'client_count' => $group->count(),
                    'expiring_soon' => $group->filter(fn($c) => $c->isAnyServiceExpiringSoon())->count(),
                    'expired' => $group->filter(fn($c) => $c->isAnyServiceExpired())->count(),
                ];
            });

        // Upcoming expirations (next 60 days / 2 months) - using domain expiration
        $upcomingExpirations = ClientData::with(['server', 'domainRegister', 'domain'])
            ->whereHas('domain', function($q) {
                $q->where('expired_date', '<=', now()->addDays(60))
                  ->where('expired_date', '>', now()->subDays(1)); // Include recent expired
            })
            ->get()
            ->sortBy(function($client) {
                return $client->domain ? $client->domain->expired_date : null;
            });

        return view('admin.client-data.service-status', compact('overview', 'serverStats', 'registerStats', 'upcomingExpirations'));
    }

    /**
     * Export client data to Excel
     */
    public function export(Request $request)
    {
        // Implementation for Excel export can be added here
        return response()->json(['message' => 'Export feature coming soon']);
    }
}
