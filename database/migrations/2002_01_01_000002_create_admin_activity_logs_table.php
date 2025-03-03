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
        Schema::create('admin_activity_logs', function (Blueprint $table) {
            /** -------------------------------
             *  Primary Key
             * ------------------------------- */
            $table->id()->comment('Primary key of the admin activity logs table');

            /** -------------------------------
             *  Action Details
             * ------------------------------- */
            $table->string('module', 100)->index()->comment('Module where the action was performed');
            $table->string('action', 150)->comment('Specific action executed by the admin');
            $table->text('description')->nullable()->comment('Detailed explanation of the action');
            $table->json('data')->nullable()->comment('Stores relevant data (created, updated, or deleted) in JSON format');
            $table->string('model_class')->comment('Fully qualified model class affected by the action');
            $table->string('route')->comment('The URL route where the action was performed');
            $table->string('route_method', 10)->comment('HTTP method used for the request (GET, POST, etc.)');

            /** -------------------------------
             *  Admin Information
             * ------------------------------- */
            $table->unsignedBigInteger('admin_id')->comment('Admin responsible for the action');
            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');

            /** -------------------------------
             *  Location and Device Information
             * ------------------------------- */
            $table->string('ipv6', 45)->nullable()->index()->comment('Admin’s IPv6 address at the time of action');
            $table->string('ipv4', 45)->nullable()->index()->comment('Admin’s IPv4 address at the time of action');
            $table->decimal('latitude', 10, 7)->nullable()->comment('Latitude coordinate at the time of action');
            $table->decimal('longitude', 10, 7)->nullable()->comment('Longitude coordinate at the time of action');
            $table->string('internet_service_provider', 255)->nullable()->comment('Admin’s ISP details');
            $table->text('client_information')->nullable()->comment('Detailed client device, browser, or OS information');

            /** -------------------------------
             *  Additional Tracking
             * ------------------------------- */
            $table->string('user_agent')->nullable()->comment('User agent string of the browser or application used');
            $table->string('session_id')->nullable()->comment('Session ID related to the action');

            /** -------------------------------
             *  Status & Error Handling
             * ------------------------------- */
            $table->boolean('status')->default(true)->comment('Indicates if the action was successful (true) or failed (false)');
            $table->string('message')->nullable()->comment('Message describing the action outcome');

            /** -------------------------------
             *  Timestamps & Soft Deletes
             * ------------------------------- */
            $table->timestamps();
            $table->softDeletes()->comment('Soft delete timestamp for log entries');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_activity_logs');
    }
};
