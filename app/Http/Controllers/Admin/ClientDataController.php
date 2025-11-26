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
        $query = ClientData::with(['user', 'domains']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
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

        // Get counts for status badges
        $statusCounts = [
            'all' => ClientData::count(),
            'active' => ClientData::where('status', 'active')->count(),
            'expired' => ClientData::where('status', 'expired')->count(),
            'warning' => ClientData::where('status', 'warning')->count(),
        ];

        return view('admin.client-data.index', compact('clients', 'statusCounts'));
    }

    /**
     * Show the form for creating new client data
     */
    public function create()
    {
        $users = User::where('role', 'client')->orderBy('name')->get();

        return view('admin.client-data.create', compact('users'));
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
        $users = User::where('role', 'client')->orderBy('name')->get();
        
        // Get domains owned by this client
        $clientDomains = Domain::with('server')
            ->where('client_id', $client->id)
            ->orderBy('domain_name')
            ->get();

        return view('admin.client-data.edit', compact('client', 'users', 'clientDomains'));
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
        $clients = ClientData::with(['domains'])->get();
        
        $overview = [
            'total_clients' => $clients->count(),
            'expiring_soon' => $clients->filter(fn($c) => $c->isAnyServiceExpiringSoon())->count(),
            'expired_services' => $clients->filter(fn($c) => $c->isAnyServiceExpired())->count(),
            'total_domains' => Domain::count(),
            'domains_expiring' => Domain::whereNotNull('expired_date')
                ->where('expired_date', '<=', now()->addDays(60))
                ->where('expired_date', '>', now())
                ->count(),
        ];

        // Server statistics from domains
        $serverStats = Domain::whereNotNull('server_id')
            ->with('server')
            ->get()
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
                    'domain_count' => $group->count(),
                    'expiring_soon' => $group->filter(fn($d) => $d->expired_date && $d->expired_date->lte(now()->addDays(60)) && $d->expired_date->gt(now()))->count(),
                    'expired' => $group->filter(fn($d) => $d->expired_date && $d->expired_date->isPast())->count(),
                ];
            });

        // Domain Register statistics from domains
        $registerStats = Domain::whereNotNull('domain_register_id')
            ->with('domainRegister')
            ->get()
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
                    'domain_count' => $group->count(),
                    'expiring_soon' => $group->filter(fn($d) => $d->expired_date && $d->expired_date->lte(now()->addDays(60)) && $d->expired_date->gt(now()))->count(),
                    'expired' => $group->filter(fn($d) => $d->expired_date && $d->expired_date->isPast())->count(),
                ];
            });

        // Upcoming expirations (next 60 days / 2 months) - using domains
        $upcomingExpirations = ClientData::with(['domains'])
            ->whereHas('domains', function($q) {
                $q->where('expired_date', '<=', now()->addDays(60))
                  ->where('expired_date', '>', now()->subDays(1)); // Include recent expired
            })
            ->get()
            ->sortBy(function($client) {
                return $client->earliest_expiration;
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
