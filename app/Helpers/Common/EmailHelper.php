<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mime\Part\TextPart;

class EmailHelper
{
    /**
     * Send email with the provided settings, template, and recipient
     *
     * @param object $emailSettings The email settings object containing SMTP and template information
     * @param string $template The HTML template for the email
     * @param object $recipient The recipient user or admin object with email and name
     * @return \Illuminate\Http\JsonResponse
     */
    public static function sendEmail($emailSettings, $template, $recipients)
    {
        try {
            // Configure SMTP settings dynamically
            config([
                'mail.mailers.smtp.host' => $emailSettings->smtp_host,
                'mail.mailers.smtp.port' => $emailSettings->smtp_port,
                'mail.mailers.smtp.username' => $emailSettings->smtp_username,
                'mail.mailers.smtp.password' => $emailSettings->smtp_password,
                'mail.mailers.smtp.encryption' => $emailSettings->smtp_encryption ?? 'tls',
                'mail.from.address' => $emailSettings->from_email,
                'mail.from.name' => $emailSettings->from_name,
            ]);

            // Create a new Mime TextPart to properly set the body
            $htmlPart = new TextPart($template, 'utf-8', 'html');

            // Send email
            Mail::send([], [], function ($message) use ($recipients, $emailSettings, $htmlPart) {
                foreach ($recipients as $recipient) {
                    $message->to($recipient->email, $recipient->name);
                }
                $message->subject($emailSettings->subject)
                    ->setBody($htmlPart); // Set the body with Mime Part
            });

            // If email is sent successfully, return a success response
            return response()->json([
                'success' => true,
                'message' => 'Email sent successfully.'
            ], 200);
        } catch (\Exception $e) {
            // Return error response if email sending fails
            return response()->json([
                'success' => false,
                'message' => 'Error sending email: ' . $e->getMessage()
            ], 500);
        }
    }
}
