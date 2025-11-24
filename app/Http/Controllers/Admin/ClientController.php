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
        
        // Use User model instead of Client model for consistency
        $clients = User::where('role', 'client')
            ->with(['services'])
            ->when($q, fn($b) => $b->where('name','like',"%$q%")->orWhere('email','like',"%$q%"))
            ->orderBy('created_at','desc')
            ->paginate(15)
            ->withQueryString();
            
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
        $client->load(['services', 'invoices']);
        return view('admin.clients.show', compact('client'));
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
        // Check if client has services or invoices
        if ($client->services()->count() > 0 || $client->invoices()->count() > 0) {
            return redirect()->route('admin.clients.index')
                ->with('error', 'Cannot delete client with existing services or invoices');
        }
        
        $client->delete();
        return redirect()->route('admin.clients.index')->with('success', 'Client deleted successfully');
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
