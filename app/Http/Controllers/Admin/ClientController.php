<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');
        $clients = Client::when($q, fn($b) => $b->where('name','like',"%$q%")->orWhere('email','like',"%$q%"))
            ->orderBy('created_at','desc')->paginate(15)->withQueryString();
        return view('admin.clients.index', compact('clients'));
    }

    public function create()
    {
        return view('admin.clients.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'=>'required|string|max:191',
            'email'=>'required|email|unique:clients,email',
            'phone'=>'nullable|string|max:50',
            'status'=>'required|in:Active,Suspended,Cancelled',
        ]);
        Client::create($data);
        return redirect()->route('admin.clients.index')->with('success','Client created');
    }

    public function show(Client $client)
    {
        return view('admin.clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        return view('admin.clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $data = $request->validate([
            'name'=>'required|string|max:191',
            'email'=>['required','email', \Illuminate\Validation\Rule::unique('clients','email')->ignore($client->id)],
            'phone'=>'nullable|string|max:50',
            'status'=>'required|in:Active,Suspended,Cancelled',
        ]);
        $client->update($data);
        return redirect()->route('admin.clients.index')->with('success','Client updated');
    }

    public function destroy(Client $client)
    {
        $client->delete();
        return redirect()->route('admin.clients.index')->with('success','Client deleted');
    }
}
