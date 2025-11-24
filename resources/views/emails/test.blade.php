<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Email</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .success-badge {
            background: #28a745;
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            display: inline-block;
            margin: 20px 0;
            font-weight: bold;
        }
        .info-box {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 15px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎉 Test Email Berhasil!</h1>
        <p>Gmail SMTP Configuration</p>
    </div>
    
    <div class="content">
        <div class="success-badge">
            ✅ Konfigurasi Berhasil
        </div>
        
        <h2>Selamat!</h2>
        <p>Jika Anda menerima email ini, berarti konfigurasi Gmail SMTP di aplikasi Laravel Anda sudah berhasil!</p>
        
        <div class="info-box">
            <h3>📋 Informasi Konfigurasi:</h3>
            <ul>
                <li><strong>SMTP Server:</strong> smtp.gmail.com</li>
                <li><strong>Port:</strong> 587</li>
                <li><strong>Encryption:</strong> TLS</li>
                <li><strong>Authentication:</strong> App Password</li>
            </ul>
        </div>
        
        <h3>🚀 Langkah Selanjutnya:</h3>
        <ol>
            <li>Aplikasi Anda sudah siap mengirim email</li>
            <li>Anda bisa menggunakan fitur reset password, notifikasi, dll</li>
            <li>Pastikan untuk tidak membagikan App Password Gmail Anda</li>
            <li>Monitor penggunaan email di Google Account Settings</li>
        </ol>
        
        <div class="info-box">
            <p><strong>💡 Tips:</strong> Untuk production, pertimbangkan menggunakan layanan email khusus seperti SendGrid, Mailgun, atau Amazon SES untuk reliability yang lebih baik.</p>
        </div>
    </div>
    
    <div class="footer">
        <p>Email ini dikirim dari aplikasi Laravel Anda</p>
        <p>{{ now()->format('d M Y, H:i:s') }}</p>
    </div>
</body>
</html>
