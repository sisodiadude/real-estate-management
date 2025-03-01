<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('email_configs', function (Blueprint $table) {
            $table->id();  // Primary key for the email configuration record
            $table->string('module')->comment('Associated module (e.g., user, order, notification)');  // Associated module (e.g., user, order)
            $table->string('subject')->comment('Subject of the email');  // Email subject
            $table->string('action')->comment('Triggering action (e.g., welcome, reset_password)');  // Action that triggers the email
            $table->longText('template')->comment('HTML email template content');  // Email template (HTML content)
            $table->string('smtp_host')->comment('SMTP server hostname or IP address');  // SMTP server host (e.g., smtp.mailtrap.io)
            $table->integer('smtp_port')->comment('SMTP server port number (e.g., 587, 465)');  // SMTP server port (e.g., 587 for TLS)
            $table->string('smtp_username')->comment('SMTP username for authentication');  // SMTP username
            $table->string('smtp_password')->comment('SMTP password for authentication (hashed for security)');  // SMTP password (hashed)
            $table->string('smtp_encryption', 10)->nullable()->comment('SMTP encryption type (e.g., tls, ssl)');  // SMTP encryption type (tls, ssl)
            $table->string('from_email')->comment('Sender\'s email address');  // Sender\'s email address (e.g., no-reply@example.com)
            $table->string('from_name')->comment('Sender\'s name to be shown in the email');  // Sender\'s name (e.g., "Company Name")
            $table->timestamps();  // Timestamps for creation and update

            // Index to speed up lookups for email configurations based on the module and action
            $table->index(['module', 'action']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_configs');
    }
};
