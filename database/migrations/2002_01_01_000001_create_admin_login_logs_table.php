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
        Schema::create('admin_login_logs', function (Blueprint $table) {
            $table->id()->comment('Primary key of the admin login logs table');

            // Action Details
            $table->string('module', 100)->index()->comment('Module where the admin action occurred');
            $table->string('action', 150)->comment('Specific action performed by the admin');

            // Admin Information
            $table->string('username')->nullable()->comment('Username of the admin performing the action');
            $table->unsignedBigInteger('admin_id')->nullable()->comment('Reference to the admin who performed the action');
            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');

            // Status and Error Handling
            $table->boolean('status')->default(true)->comment('Indicates if the action was successful (true) or failed (false)');
            $table->string('message')->nullable()->comment('Message of the action');

            // Location and Device Information
            $table->string('ipv6', 45)->nullable()->index()->comment('IPv6 address of the admin at the time of login');
            $table->string('ipv4', 45)->nullable()->index()->comment('IPv4 address of the admin at the time of login');
            $table->decimal('latitude', 10, 7)->nullable()->comment('Latitude at the time of admin action');
            $table->decimal('longitude', 10, 7)->nullable()->comment('Longitude at the time of admin action');
            $table->string('internet_service_provider', 255)->nullable()->comment('ISP used by the admin');
            $table->text('client_information')->nullable()->comment('Detailed information about the client device, browser, or OS used by the admin');

            // Additional Security & Tracking
            $table->string('user_agent')->nullable()->comment('User agent string of the browser or application used for login');
            $table->string('session_id')->nullable()->comment('Session ID associated with the login for tracking');
            $table->string('login_method', 50)->nullable()->comment('Method of login (password, OTP, social login, etc.)');

            // Timestamps & Soft Deletes
            $table->timestamps();
            $table->softDeletes()->comment('Soft delete timestamp for log entries');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_login_logs');
    }
};
