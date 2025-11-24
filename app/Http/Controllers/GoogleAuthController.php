<?php

namespace App\Http\Controllers;

use App\Services\GmailService;
use Illuminate\Http\Request;

class GoogleAuthController extends Controller
{
    private $gmailService;

    public function __construct(GmailService $gmailService)
    {
        $this->gmailService = $gmailService;
    }

    public function redirectToGoogle()
    {
        $authUrl = $this->gmailService->getAuthUrl();
        return redirect($authUrl);
    }

    public function handleGoogleCallback(Request $request)
    {
        $code = $request->get('code');
        
        if (!$code) {
            return redirect('/')->with('error', 'Authorization code not found.');
        }

        try {
            $token = $this->gmailService->authenticate($code);
            
            // Calculate token duration
            $expiresIn = isset($token['expires_in']) ? $token['expires_in'] : 3600;
            $durationDays = round($expiresIn / 86400, 1);
            
            $message = "Gmail authentication successful! Token duration: {$durationDays} days. You can now send emails via Gmail API.";
            
            return redirect('/gmail-test')->with('success', $message);
        } catch (\Exception $e) {
            return redirect('/')->with('error', 'Authentication failed: ' . $e->getMessage());
        }
    }

    public function sendTestEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        try {
            if (!$this->gmailService->isAuthenticated()) {
                return redirect()->route('google.auth')->with('error', 'Please authenticate with Google first.');
            }

            $result = $this->gmailService->sendEmail(
                $request->email,
                'Test Email - Gmail API OAuth2',
                $this->getTestEmailBody()
            );

            return back()->with('success', 'Test email sent successfully via Gmail API!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }

    private function getTestEmailBody()
    {
        return '
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; }
                .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 10px; border-radius: 5px; margin: 10px 0; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>🎉 Gmail API OAuth2 Test</h1>
            </div>
            <div class="content">
                <div class="success">
                    ✅ Email berhasil dikirim menggunakan Gmail API dengan OAuth2!
                </div>
                <p>Selamat! Konfigurasi Gmail API dengan OAuth2 sudah berhasil.</p>
                <p><strong>Keuntungan OAuth2:</strong></p>
                <ul>
                    <li>Lebih aman daripada App Password</li>
                    <li>Token dapat di-refresh otomatis</li>
                    <li>Kontrol akses yang lebih granular</li>
                    <li>Dapat dicabut kapan saja dari Google Account</li>
                </ul>
                <p>Aplikasi Laravel Anda sekarang siap mengirim email melalui Gmail API!</p>
                <hr>
                <small>Dikirim pada: ' . now()->format('d M Y, H:i:s') . '</small>
            </div>
        </body>
        </html>';
    }
}
