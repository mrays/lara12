<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gmail API OAuth2 Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen py-8">
    <div class="max-w-4xl mx-auto px-4">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-500 to-green-500 text-white p-6">
                <h1 class="text-3xl font-bold">📧 Gmail API OAuth2 Test</h1>
                <p class="mt-2 opacity-90">Test konfigurasi Gmail API dengan OAuth2 authentication</p>
            </div>

            <div class="p-6">
                <!-- Alert Messages -->
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                        <div class="flex items-center">
                            <span class="text-xl mr-2">✅</span>
                            <span>{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                        <div class="flex items-center">
                            <span class="text-xl mr-2">❌</span>
                            <span>{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                <!-- OAuth2 Configuration Info -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                    <h2 class="text-xl font-semibold text-blue-800 mb-4">🔐 Konfigurasi OAuth2</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <strong>Client ID:</strong><br>
                            <code class="bg-gray-100 px-2 py-1 rounded text-xs">{{ substr(config('services.google.client_id'), 0, 30) }}...</code>
                        </div>
                        <div>
                            <strong>Redirect URI:</strong><br>
                            <code class="bg-gray-100 px-2 py-1 rounded text-xs">{{ config('services.google.redirect_uri') }}</code>
                        </div>
                    </div>
                </div>

                <!-- Authentication Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Authentication -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h2 class="text-xl font-semibold mb-4">🔑 Authentication</h2>
                        <p class="text-gray-600 mb-4">Langkah pertama adalah melakukan authentication dengan Google OAuth2.</p>
                        
                        <a href="{{ route('google.auth') }}" 
                           class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition duration-200">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z" clip-rule="evenodd"></path>
                            </svg>
                            Authenticate dengan Google
                        </a>
                        
                        <div class="mt-4 text-sm text-gray-500">
                            <p><strong>Catatan:</strong></p>
                            <ul class="list-disc list-inside mt-2 space-y-1">
                                <li>Anda akan diarahkan ke Google untuk login</li>
                                <li>Berikan izin akses Gmail untuk aplikasi</li>
                                <li>Setelah berhasil, Anda akan kembali ke aplikasi</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Test Email -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h2 class="text-xl font-semibold mb-4">📧 Test Email</h2>
                        <p class="text-gray-600 mb-4">Kirim test email untuk memverifikasi konfigurasi Gmail API.</p>
                        
                        <form action="{{ route('test.gmail.api') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Tujuan:</label>
                                <input type="email" 
                                       id="email" 
                                       name="email" 
                                       required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="contoh@email.com">
                            </div>
                            
                            <button type="submit" 
                                    class="w-full inline-flex items-center justify-center px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                                Kirim Test Email
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="mt-8 bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                    <h2 class="text-xl font-semibold text-yellow-800 mb-4">📋 Petunjuk Penggunaan</h2>
                    <div class="space-y-4 text-sm text-yellow-700">
                        <div>
                            <h3 class="font-semibold">1. Setup Google Cloud Console:</h3>
                            <ul class="list-disc list-inside ml-4 mt-2 space-y-1">
                                <li>Buat project di <a href="https://console.cloud.google.com/" target="_blank" class="underline">Google Cloud Console</a></li>
                                <li>Aktifkan Gmail API</li>
                                <li>Buat OAuth 2.0 Client ID di Credentials</li>
                                <li>Tambahkan redirect URI: <code class="bg-yellow-100 px-1 rounded">{{ config('services.google.redirect_uri') }}</code></li>
                            </ul>
                        </div>
                        
                        <div>
                            <h3 class="font-semibold">2. Konfigurasi .env:</h3>
                            <pre class="bg-yellow-100 p-3 rounded text-xs overflow-x-auto mt-2"><code>GOOGLE_CLIENT_ID={{ config('services.google.client_id') }}
GOOGLE_CLIENT_SECRET={{ config('services.google.client_secret') }}
GOOGLE_REDIRECT_URI={{ config('services.google.redirect_uri') }}</code></pre>
                        </div>
                        
                        <div>
                            <h3 class="font-semibold">3. Command Line Usage:</h3>
                            <pre class="bg-yellow-100 p-3 rounded text-xs overflow-x-auto mt-2"><code># Generate auth URL
php artisan gmail:auth url

# Check auth status  
php artisan gmail:auth status

# Send test email
php artisan gmail:auth test</code></pre>
                        </div>
                    </div>
                </div>

                <!-- Back to Dashboard -->
                <div class="mt-8 text-center">
                    <a href="{{ route('dashboard') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
