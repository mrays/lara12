<?php

namespace App\Http\Controllers;

use App\Models\ServicePackage;
use App\Models\DomainExtension;
use App\Models\Client;
use App\Models\User;
use App\Models\Service;
use App\Models\Invoice;
use App\Models\Domain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Carbon\Carbon;

class OrderController extends Controller
{
    /**
     * Show domain selection page
     */
    public function chooseDomain()
    {
        // Get available domain extensions
        $extensions = DomainExtension::where('is_active', true)->get();
        
        return view('order.choose-domain', compact('extensions'));
    }

    /**
     * Process domain selection
     */
    public function selectDomain(Request $request)
    {
        $request->validate([
            'domain_name' => 'required|string|min:3|max:63|regex:/^[a-zA-Z0-9-]+$/',
            'extension_id' => 'required|exists:domain_extensions,id'
        ]);

        // Get extension
        $extension = DomainExtension::findOrFail($request->extension_id);
        $fullDomain = $request->domain_name . '.' . $extension->extension;

        // Check if domain already exists
        if (Domain::where('domain_name', $fullDomain)->exists()) {
            return back()
                ->withInput()
                ->with('error', "Domain {$fullDomain} is already registered. Please choose a different domain name.");
        }

        // Store in session
        Session::put('order.domain_name', $request->domain_name);
        Session::put('order.extension_id', $request->extension_id);
        Session::put('order.full_domain', $fullDomain);
        Session::put('order.extension_price', $extension->price);

        return redirect()->route('order.select-template');
    }

    /**
     * Show template selection page
     */
    public function selectTemplate()
    {
        if (!Session::has('order.domain_name')) {
            return redirect()->route('order.choose-domain');
        }

        // Get templates from service packages (temporary solution)
        // You can create separate templates table later
        $servicePackages = ServicePackage::where('is_active', true)->get();
        
        $templates = [];
        foreach ($servicePackages->take(3) as $index => $package) {
            $templates[] = [
                'id' => $package->id,
                'name' => $package->name . ' Template',
                'description' => $package->description ?? 'Professional website template',
                'category' => 'Business',
                'preview_image' => '/images/templates/template' . ($index + 1) . '.jpg',
                'is_free' => $package->base_price == 0,
                'price' => $package->base_price > 100000 ? 500000 : 0 // Template price logic
            ];
        }
        
        // Add default templates if no service packages
        if (empty($templates)) {
            $templates = [
                [
                    'id' => 1,
                    'name' => 'Business Template',
                    'description' => 'Professional business website template',
                    'category' => 'Business',
                    'preview_image' => '/images/templates/business.jpg',
                    'is_free' => true,
                    'price' => 0
                ]
            ];
        }

        $domainExtension = DomainExtension::findOrFail(Session::get('order.extension_id'));
        
        return view('order.select-template', compact('templates', 'domainExtension'));
    }

    /**
     * Process template selection
     */
    public function postSelectTemplate(Request $request)
    {
        $request->validate([
            'template_id' => 'required|integer'
        ]);

        // Store template selection in session
        Session::put('order.template_id', $request->template_id);
        
        // Get template data from service packages
        if ($request->template_id == 0) {
            $selectedTemplate = ['name' => 'No Template', 'price' => 0];
        } else {
            $servicePackages = ServicePackage::where('is_active', true)->get();
            $package = $servicePackages->find($request->template_id);
            
            if ($package) {
                $selectedTemplate = [
                    'name' => $package->name . ' Template',
                    'price' => $package->base_price > 100000 ? 500000 : 0
                ];
            } else {
                $selectedTemplate = ['name' => 'Business Template', 'price' => 0];
            }
        }
        Session::put('order.template_name', $selectedTemplate['name']);
        Session::put('order.template_price', $selectedTemplate['price']);

        return redirect()->route('order.customer-details');
    }

    /**
     * Show package selection page (now after login)
     */
    public function selectPackage()
    {
        // Check if user is logged in
        if (!auth()->check()) {
            return redirect()->route('order.customer-details')
                ->with('error', 'Please complete your account registration first.');
        }

        if (!Session::has('order.domain_name')) {
            return redirect()->route('order.choose-domain');
        }

        $packages = ServicePackage::where('is_active', true)->get();
        $domainExtension = DomainExtension::findOrFail(Session::get('order.extension_id'));
        $orderData = Session::get('order');
        
        return view('order.select-package', compact('packages', 'domainExtension', 'orderData'));
    }

    /**
     * Show customer details form
     */
    public function customerDetails()
    {
        if (!Session::has('order.domain_name')) {
            return redirect()->route('order.choose-domain');
        }

        $orderData = Session::get('order');
        
        return view('order.customer-details', compact('orderData'));
    }

    /**
     * Register customer and login
     */
    public function registerCustomer(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:500',
            'password' => 'required|string|min:6|confirmed',
        ]);

        try {
            // Create user account (for authentication and business data)
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'password' => bcrypt($request->password),
                'role' => 'client',
                'status' => 'Active',
                'email_verified_at' => now(),
            ]);

            // Store user ID as client ID in session (using User as Client)
            Session::put('order.client_id', $user->id);
            Session::put('order.user_id', $user->id);
            Session::put('order.client_name', $user->name);
            Session::put('order.client_email', $user->email);

            // Log in the new user
            auth()->login($user);

            // Redirect to package selection
            return redirect()->route('order.select-package');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create account. Please try again. Error: ' . $e->getMessage());
        }
    }

    /**
     * Show checkout page (user is now logged in)
     */
    public function checkout()
    {
        // Check if user is logged in
        if (!auth()->check()) {
            return redirect()->route('order.customer-details')
                ->with('error', 'Please complete your account registration first.');
        }

        // Check if order data exists
        if (!Session::has('order.package_id') || !Session::has('order.full_domain')) {
            return redirect()->route('order.choose-domain')
                ->with('error', 'Order session expired. Please start again.');
        }

        $orderData = Session::get('order');
        $user = auth()->user();
        
        return view('order.checkout', compact('orderData', 'user'));
    }

    /**
     * Submit the order and create invoice
     */
    public function submitOrder(Request $request)
    {
        // Check if user is logged in
        if (!auth()->check()) {
            return redirect()->route('order.customer-details')
                ->with('error', 'Please complete your account registration first.');
        }

        // Validate package selection
        $request->validate([
            'package_id' => 'required|exists:service_packages,id'
        ]);

        // Get package and store in session
        $package = ServicePackage::findOrFail($request->package_id);
        Session::put('order.package_id', $request->package_id);
        Session::put('order.package_name', $package->name);
        Session::put('order.package_price', $package->base_price);
        
        // Calculate total price
        $domainPrice = Session::get('order.extension_price', 0);
        $templatePrice = Session::get('order.template_price', 0);
        $totalPrice = $package->base_price + $domainPrice + $templatePrice;
        Session::put('order.total_price', $totalPrice);

        // Check if order data exists
        if (!Session::has('order.full_domain')) {
            return redirect()->route('order.choose-domain')
                ->with('error', 'Order session expired. Please start again.');
        }

        try {
            DB::beginTransaction();

            $user = auth()->user();
            $clientId = Session::get('order.client_id');

            // Re-check if domain is still available
            if (Domain::where('domain_name', Session::get('order.full_domain'))->exists()) {
                DB::rollBack();
                Session::forget('order');
                return redirect()->route('order.choose-domain')
                    ->with('error', 'Sorry, this domain has been taken by another user. Please choose a different domain.');
            }

            // Create domain record
            $domain = Domain::create([
                'domain_name' => Session::get('order.full_domain'),
                'client_id' => $clientId,
                'domain_register_id' => DB::table('domain_registers')->first()->id ?? null,
                'server_id' => DB::table('servers')->first()->id ?? null,
                'expired_date' => Carbon::now()->addYear(),
                'status' => 'active',
                'notes' => 'Created from guest order',
            ]);

            // Create service record
            $service = Service::create([
                'client_id' => $clientId,
                'package_id' => Session::get('order.package_id'),
                'domain_id' => $domain->id,
                'product' => Session::get('order.package_name'),
                'domain' => Session::get('order.full_domain'),
                'status' => 'Active',
                'due_date' => Carbon::now()->addYear(),
            ]);

            // Create invoice
            $invoice = Invoice::create([
                'client_id' => $clientId,
                'service_id' => $service->id,
                'invoice_number' => 'INV-' . date('Y') . '-' . str_pad(Invoice::count() + 1, 5, '0', STR_PAD_LEFT),
                'due_date' => Carbon::now()->addDays(7),
                'amount' => Session::get('order.total_price'),
                'status' => 'Unpaid',
                'description' => 'Registration for ' . Session::get('order.full_domain') . ' - ' . Session::get('order.package_name'),
            ]);

            // Clear session
            Session::forget('order');

            DB::commit();

            // Redirect to client dashboard with success message
            return redirect()->route('client.dashboard')
                ->with('success', 'Order completed successfully! Please complete your payment to activate services.')
                ->with('invoice_id', $invoice->id);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->with('error', 'Failed to create order. Please try again. Error: ' . $e->getMessage());
        }
    }

    /**
     * Show success page
     */
    public function success(Invoice $invoice)
    {
        // Check if the invoice belongs to the logged-in user or if it's a guest order
        if (auth()->check() && $invoice->client_id !== auth()->id()) {
            abort(403);
        }

        return view('order.success', compact('invoice'));
    }
}
