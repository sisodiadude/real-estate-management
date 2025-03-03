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
        Schema::create('admin_departments', function (Blueprint $table) {
            $table->id();

            /** -------------------------------
             *  Basic Information
             * ------------------------------- */
            $table->unsignedBigInteger('branch_id')->comment('Branch reference');
            $table->foreign('branch_id')->references('id')->on('admin_branches')->onDelete('CASCADE');

            $table->string('department_unique_id')->unique()->comment('Unique ID of Department');
            $table->string('name')->comment('Department name');
            $table->string('slug')->unique()->comment('SEO-friendly unique identifier');
            $table->text('description')->nullable()->comment('Brief department description');

            /** -------------------------------
             *  Leadership & Management
             * ------------------------------- */
            $table->foreignId('leader_id')->nullable()->comment('Department leader admin ID');

            /** -------------------------------
             *  Contact Information
             * ------------------------------- */
            $table->string('email', 100)->unique()->nullable()->comment('Department contact email');
            $table->timestamp('email_verified_at')->nullable()->comment('Email verification timestamp');
            $table->string('mobile', 15)->unique()->nullable()->comment('Department contact mobile number');
            $table->timestamp('mobile_verified_at')->nullable()->comment('Mobile verification timestamp');

            /** -------------------------------
             *  OTP Verification
             * ------------------------------- */
            $table->string('email_change_otp', 10)->nullable()->comment('OTP for email change verification');
            $table->timestamp('email_change_otp_expires_at')->nullable()->comment('Email change OTP expiry');
            $table->string('mobile_change_otp', 10)->nullable()->comment('OTP for mobile change verification');
            $table->timestamp('mobile_change_otp_expires_at')->nullable()->comment('Mobile change OTP expiry');

            /** -------------------------------
             *  Operating Hours
             * ------------------------------- */
            $table->json('operating_hours')->nullable()->comment('Department open-close timings, e.g., {"Monday": "9am-6pm", "Sunday": "Closed"}');

            /** -------------------------------
             *  Department Status
             * ------------------------------- */
            $table->enum('status', ['active', 'inactive', 'suspended', 'archived'])
                ->default('active')
                ->comment('Department current status');

            /** -------------------------------
             *  Audit Trail (Created & Updated by)
             * ------------------------------- */
            $table->unsignedBigInteger('created_by_id')->nullable()->comment('ID of creator');
            $table->string('created_by_type')->nullable()->comment('Type of creator (e.g., App\\Models\\Admin)');
            $table->unsignedBigInteger('last_updated_by_id')->nullable()->comment('ID of last updater');
            $table->string('last_updated_by_type')->nullable()->comment('Type of last updater (e.g., App\\Models\\Admin)');

            /** -------------------------------
             *  System Fields
             * ------------------------------- */
            $table->timestamps();
            $table->softDeletes();

            /** -------------------------------
             *  Indexes for Optimization
             * ------------------------------- */
            $table->index(['email', 'mobile']);
            $table->index(['branch_id']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_departments');
    }
};
