<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');
        $status = $request->query('status');
        
        // Use User model instead of Client model for consistency
        $clients = User::where('role', 'client')
            ->when($q, function($query) use ($q) {
                $query->where(function($subQuery) use ($q) {
                    $subQuery->where('name', 'like', "%$q%")
                        ->orWhere('email', 'like', "%$q%")
                        ->orWhere('whatsapp', 'like', "%$q%")
                        ->orWhere('phone', 'like', "%$q%")
                        // Search by domain from related services using subquery
                        ->orWhereIn('id', function($domainQuery) use ($q) {
                            $domainQuery->select('client_id')
                                ->from('services')
                                ->where('domain', 'like', "%$q%");
                        });
                });
            })
            ->when($status && $status !== 'all', function($query) use ($status) {
                $query->where('status', $status);
            })
            ->orderBy('created_at','desc')
            ->get();
            
        return view('admin.clients.index', compact('clients'));
    }

    public function create()
    {
        return view('admin.clients.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:191',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:50',
            'status' => 'nullable|in:Active,Inactive',
            'password' => 'required|string|min:8|confirmed',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Create user account for client
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'role' => 'client',
            'password' => Hash::make($data['password']),
            'status' => $data['status'] ?? 'Active',
        ]);

        // You can add notes to a separate table or user meta if needed
        // For now, we'll just create the user

        return redirect()->route('admin.clients.index')->with('success', 'Client created successfully with login credentials');
    }

    public function show(User $client)
    {
        // Get services and invoices using direct queries
        $services = \DB::table('services')->where('client_id', $client->id)->get();
        $invoices = \DB::table('invoices')->where('client_id', $client->id)->get();
        
        return view('admin.clients.show', compact('client', 'services', 'invoices'));
    }

    public function edit(User $client)
    {
        return view('admin.clients.edit', compact('client'));
    }

    public function update(Request $request, User $client)
    {
        $data = $request->validate([
            'name' => 'required|string|max:191',
            'email' => ['required','email', \Illuminate\Validation\Rule::unique('users','email')->ignore($client->id)],
            'phone' => 'nullable|string|max:50',
            'status' => 'nullable|in:Active,Inactive',
        ]);
        
        $client->update($data);
        return redirect()->route('admin.clients.index')->with('success', 'Client updated successfully');
    }

    public function destroy(User $client)
    {
        // Check if client has services or invoices using direct queries
        $servicesCount = \DB::table('services')->where('client_id', $client->id)->count();
        $invoicesCount = \DB::table('invoices')->where('client_id', $client->id)->count();
        
        if ($servicesCount > 0 || $invoicesCount > 0) {
            return redirect()->route('admin.clients.index')
                ->with('error', 'Cannot delete client with existing services or invoices');
        }
        
        $client->delete();
        return redirect()->route('admin.clients.index')->with('success', 'Client deleted successfully');
    }

    /**
     * Toggle client status
     */
    public function toggleStatus(Request $request, $clientId)
    {
        $request->validate([
            'status' => 'required|in:Active,Inactive'
        ]);

        \DB::table('users')
            ->where('id', $clientId)
            ->update([
                'status' => $request->status,
                'updated_at' => now()
            ]);

        return redirect()->route('admin.clients.index')
            ->with('success', 'Client status updated successfully');
    }

    /**
     * Manage client services
     */
    public function manageServices(Request $request, $clientId)
    {
        $request->validate([
            'service_type' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:Active,Pending,Suspended,Terminated',
            'description' => 'nullable|string'
        ]);

        \DB::table('services')->insert([
            'client_id' => $clientId,
            'name' => $request->service_type,
            'price' => $request->price,
            'status' => $request->status,
            'description' => $request->description,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('admin.clients.index')
            ->with('success', 'Service added successfully');
    }

    /**
     * Get client services (AJAX)
     */
    public function getServices($clientId)
    {
        $services = \DB::table('services')
            ->where('client_id', $clientId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'services' => $services
        ]);
    }

    /**
     * Delete service (AJAX)
     */
    public function deleteService($serviceId)
    {
        \DB::table('services')->where('id', $serviceId)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Service deleted successfully'
        ]);
    }

    /**
     * Reset client password
     */
    public function resetPassword(Request $request, User $client)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $client->update([
            'password' => Hash::make($request->password),
        ]);

        // Optional: Send email notification to client
        if ($request->has('notify_client')) {
            // Mail::to($client->email)->send(new PasswordResetNotification($client));
        }

        return redirect()->route('admin.clients.index')
            ->with('success', 'Password reset successfully for ' . $client->name);
    }
}
