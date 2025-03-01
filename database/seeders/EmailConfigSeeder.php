<?php

namespace Database\Seeders;

use App\Models\EmailConfig;
use Illuminate\Database\Seeder;

class EmailConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Array of email configurations to insert
        $emailConfigs = [
            [
                'module' => 'admin-auth',
                'subject' => 'Your 2FA OTP Code',
                'action' => 'two_factor',
                'template' => '<!DOCTYPE html>
                                <html>

                                <head>
                                    <meta charset="UTF-8">
                                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                                    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
                                    <style>
                                        body {
                                            font-family: "Roboto", Helvetica, Arial, sans-serif;
                                            background-color: #f7fafc;
                                            margin: 0;
                                            padding: 0;
                                            color: #000;
                                        }

                                        .container {
                                            max-width: 600px;
                                            margin: 0 auto;
                                            padding: 16px;
                                        }

                                        .header,
                                        .footer {
                                            text-align: center;
                                            margin-bottom: 24px;
                                        }

                                        .header img,
                                        .footer img {
                                            width: 96px;
                                        }

                                        .content {
                                            background-color: #ffffff;
                                            border: 1px solid #e2e8f0;
                                            border-radius: 6px;
                                            padding: 40px;
                                            text-align: center;
                                        }

                                        .content h1 {
                                            font-size: 26px;
                                            margin-bottom: 16px;
                                            font-weight: 700;
                                        }

                                        .content p {
                                            font-size: 16px;
                                            line-height: 24px;
                                            margin: 12px 0;
                                        }

                                        .otp-code {
                                            display: inline-block;
                                            font-size: 22px;
                                            font-weight: bold;
                                            padding: 8px 16px;
                                            background-color: #e2e8f0;
                                            border-radius: 4px;
                                            margin: 16px 0;
                                        }

                                        .expiry-time {
                                            font-size: 14px;
                                            color: #e53e3e;
                                            margin-top: 8px;
                                        }

                                        .footer p {
                                            font-size: 14px;
                                            color: #718096;
                                        }

                                        @media (max-width: 600px) {
                                            .content {
                                                padding: 20px;
                                            }

                                            .content h1 {
                                                font-size: 24px;
                                            }

                                            .content p,
                                            .otp-code {
                                                font-size: 16px;
                                            }
                                        }
                                    </style>
                                </head>

                                <body>
                                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" bgcolor="#f7fafc">
                                        <tr>
                                            <td align="center">
                                                <table class="container" role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                                    <tr>
                                                        <td class="header">
                                                            <img src="https://omah.dexignzone.com/welcome/images/logo-full.png" alt="Company Logo">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="content" bgcolor="#ffffff">
                                                            <h1>Two-Factor Authentication (2FA) Code</h1>
                                                            <p>Use the following One-Time Passcode (OTP) to complete your authentication:</p>
                                                            <div class="otp-code">{{login_otp}}</div>
                                                            <p class="expiry-time">This OTP will expire in {{login_otp_expires_at}}.</p>
                                                            <p>If you did not initiate this request, please disregard this email.</p>
                                                            <p><strong>Security Advisory:</strong> Do not share your 2FA code with anyone. Our support
                                                                team will never ask for it.</p>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="footer">
                                                            <img src="https://omah.dexignzone.com/welcome/images/logo-full.png" alt="Company Logo">
                                                            <p>&copy; Hip Corp. | 1 Hip Street, Gnarly State, 01234 USA</p>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </body>

                                </html>',
                'smtp_host' => 'smtp.gmail.com',
                'smtp_port' => 587,
                'smtp_username' => 'aaravyaestates@gmail.com',
                'smtp_password' => 'oisafbspzsgjfkjf',
                'smtp_encryption' => 'tls',
                'from_email' => 'aaravyaestates@gmail.com',
                'from_name' => 'Aaravya Estates',
            ],
            [
                'module' => 'admin-auth',
                'subject' => 'Login Notification - New Device',
                'action' => 'login_notification',
                'template' => '<html><body><h1>Login Notification</h1><p>You have logged in from a new device or location.</p></body></html>',
                'smtp_host' => 'smtp.gmail.com',
                'smtp_port' => 587,
                'smtp_username' => 'aaravyaestates@gmail.com',
                'smtp_password' => 'oisafbspzsgjfkjf',
                'smtp_encryption' => 'tls',
                'from_email' => 'aaravyaestates@gmail.com',
                'from_name' => 'Aaravya Estates',
            ],
            [
                'module' => 'admin-auth',
                'subject' => 'Welcome to Aaravya Estates!',
                'action' => 'welcome',
                'template' => '<html><body><h1>Welcome to Our Service</h1><p>Thank you for registering with Aaravya Estates!</p></body></html>',
                'smtp_host' => 'smtp.gmail.com',
                'smtp_port' => 587,
                'smtp_username' => 'aaravyaestates@gmail.com',
                'smtp_password' => 'oisafbspzsgjfkjf',
                'smtp_encryption' => 'tls',
                'from_email' => 'aaravyaestates@gmail.com',
                'from_name' => 'Aaravya Estates',
            ],
            [
                'module' => 'backup',  // Email template for backup-related actions
                'subject' => 'Backup Error Occurred',  // Clear subject indicating a backup error
                'action' => 'error',  // Action triggered when a backup error occurs
                'template' => '<html><body><h1>Backup Failure Notification</h1><p>An error occurred during the backup process. Please check the logs for more details.</p></body></html>',
                'smtp_host' => 'smtp.gmail.com',  // Gmail SMTP server
                'smtp_port' => 587,  // TLS-secured port for sending email
                'smtp_username' => 'aaravyaestates@gmail.com',  // Your Gmail username
                'smtp_password' => 'oisafbspzsgjfkjf',  // Your Gmail SMTP password (hashed)
                'smtp_encryption' => 'tls',  // Encryption method for secure email transmission
                'from_email' => 'aaravyaestates@gmail.com',  // Sender's email address
                'from_name' => 'Aaravya Estates',  // Sender's name that will appear in the "From" field
            ],
        ];

        // Insert all records in one go
        EmailConfig::insert($emailConfigs);
    }
}
