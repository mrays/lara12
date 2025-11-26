<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\GmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GmailSettingsController extends Controller
{
    protected $gmailService;

    public function __construct(GmailService $gmailService)
    {
        $this->gmailService = $gmailService;
    }

    /**
     * Display Gmail settings page
     */
    public function index()
    {
        $isAuthenticated = $this->gmailService->isAuthenticated();
        $tokenInfo = $this->gmailService->getTokenInfo();

        return view('admin.settings.gmail', compact('isAuthenticated', 'tokenInfo'));
    }

    /**
     * Redirect to Google OAuth
     */
    public function authenticate()
    {
        $authUrl = $this->gmailService->getAuthUrl();
        return redirect($authUrl);
    }

    /**
     * Handle Google OAuth callback
     */
    public function callback(Request $request)
    {
        $code = $request->get('code');

        if (!$code) {
            return redirect()->route('admin.settings.gmail')
                ->with('error', 'Authorization code not found. Please try again.');
        }

        try {
            $token = $this->gmailService->authenticate($code);

            $hasRefreshToken = isset($token['refresh_token']);
            $message = $hasRefreshToken 
                ? 'Gmail authentication successful! Auto-refresh enabled - token will never expire.'
                : 'Gmail authentication successful! Note: No refresh token received.';

            return redirect()->route('admin.settings.gmail')
                ->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route('admin.settings.gmail')
                ->with('error', 'Authentication failed: ' . $e->getMessage());
        }
    }

    /**
     * Revoke Gmail token
     */
    public function revoke()
    {
        try {
            // Delete the token file
            if (Storage::exists('gmail_token.json')) {
                Storage::delete('gmail_token.json');
            }

            return redirect()->route('admin.settings.gmail')
                ->with('success', 'Gmail token has been revoked successfully. You will need to re-authenticate to send emails.');
        } catch (\Exception $e) {
            return redirect()->route('admin.settings.gmail')
                ->with('error', 'Failed to revoke token: ' . $e->getMessage());
        }
    }

    /**
     * Send test email
     */
    public function sendTestEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        try {
            if (!$this->gmailService->isAuthenticated()) {
                return redirect()->route('admin.settings.gmail')
                    ->with('error', 'Please authenticate with Google first.');
            }

            $this->gmailService->sendEmail(
                $request->email,
                'Test Email - Exputra Billing Gmail API',
                $this->getTestEmailBody()
            );

            return redirect()->route('admin.settings.gmail')
                ->with('success', 'Test email sent successfully to ' . $request->email);
        } catch (\Exception $e) {
            return redirect()->route('admin.settings.gmail')
                ->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }

    /**
     * Get test email HTML body
     */
    private function getTestEmailBody()
    {
        $appName = config('app.name', 'Exputra Billing');
        $timestamp = now()->format('d M Y, H:i:s');

        return '
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
                .container { max-width: 600px; margin: 0 auto; }
                .header { background: linear-gradient(135deg, #696cff 0%, #5a5ee0 100%); color: white; padding: 30px; text-align: center; }
                .header h1 { margin: 0; font-size: 24px; }
                .content { padding: 30px; background: #f8f9fa; }
                .success-box { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
                .info-box { background: #e7f3ff; border: 1px solid #b6d4fe; color: #084298; padding: 15px; border-radius: 8px; }
                .footer { padding: 20px; text-align: center; color: #6c757d; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>🎉 ' . $appName . '</h1>
                    <p style="margin: 10px 0 0 0; opacity: 0.9;">Gmail API Test Email</p>
                </div>
                <div class="content">
                    <div class="success-box">
                        <strong>✅ Email berhasil dikirim!</strong><br>
                        Konfigurasi Gmail API OAuth2 Anda berfungsi dengan baik.
                    </div>
                    <div class="info-box">
                        <strong>ℹ️ Informasi:</strong>
                        <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                            <li>Email ini dikirim menggunakan Gmail API dengan OAuth2</li>
                            <li>Token akan otomatis di-refresh sehingga tidak perlu re-authenticate</li>
                            <li>Semua email sistem akan dikirim melalui metode ini</li>
                        </ul>
                    </div>
                </div>
                <div class="footer">
                    <p>Dikirim pada: ' . $timestamp . '</p>
                    <p>' . $appName . ' - Grow Your Business Now</p>
                </div>
            </div>
        </body>
        </html>';
    }
}
