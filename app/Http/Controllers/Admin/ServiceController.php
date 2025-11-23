<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Client;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');
        $services = Service::with('client')
            ->when($q, fn($b) => $b->where('product','like',"%$q%")->orWhere('domain','like',"%$q%"))
            ->orderBy('due_date','asc')->paginate(15)->withQueryString();
        return view('admin.services.index', compact('services'));
    }

    public function create()
    {
        $clients = Client::orderBy('name')->get();
        return view('admin.services.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id'=>'required|exists:clients,id',
            'product'=>'required|string|max:191',
            'domain'=>'nullable|string|max:191',
            'price'=>'required|numeric',
            'billing_cycle'=>'nullable|string|max:50',
            'registration_date'=>'nullable|date',
            'due_date'=>'nullable|date',
            'ip'=>'nullable|ip',
            'status'=>'required|in:Active,Suspended,Cancelled',
        ]);
        Service::create($data);
        return redirect()->route('admin.services.index')->with('success','Service created');
    }

    public function show(Service $service)
    {
        $service->load('client');
        return view('admin.services.show', compact('service'));
    }

    public function edit(Service $service)
    {
        $clients = Client::orderBy('name')->get();
        return view('admin.services.edit', compact('service','clients'));
    }

    public function update(Request $request, Service $service)
    {
        $data = $request->validate([
            'client_id'=>'required|exists:clients,id',
            'product'=>'required|string|max:191',
            'domain'=>'nullable|string|max:191',
            'price'=>'required|numeric',
            'billing_cycle'=>'nullable|string|max:50',
            'registration_date'=>'nullable|date',
            'due_date'=>'nullable|date',
            'ip'=>'nullable|ip',
            'status'=>'required|in:Active,Suspended,Cancelled',
        ]);
        $service->update($data);
        return redirect()->route('admin.services.index')->with('success','Service updated');
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return redirect()->route('admin.services.index')->with('success','Service deleted');
    }
}
