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
        Schema::create('admin_branches', function (Blueprint $table) {
            $table->id();

            /** -------------------------------
             *  Basic Information
             * ------------------------------- */
            $table->string('branch_unique_id')->unique()->comment('Uniqe ID of Branch');
            $table->string('name')->comment('Unique branch name');
            $table->string('slug')->unique()->comment('SEO-friendly unique identifier');
            $table->text('description')->nullable()->comment('Brief branch description');
            $table->date('date_of_start')->nullable()->comment('Date the branch was established');
            $table->string('logo', 255)->nullable()->comment('Branch logo URL');

            /** -------------------------------
             *  Leadership & Management
             * ------------------------------- */
            $table->foreignId('leader_id')->nullable()->comment('Branch leader admin ID');

            /** -------------------------------
             *  Contact Information
             * ------------------------------- */
            $table->string('email', 100)->unique()->comment('Branch contact email');
            $table->timestamp('email_verified_at')->nullable()->comment('Email verification timestamp');
            $table->string('mobile', 15)->unique()->comment('Branch contact mobile number');
            $table->timestamp('mobile_verified_at')->nullable()->comment('Mobile verification timestamp');

            /** -------------------------------
             *  OTP Verification
             * ------------------------------- */
            $table->string('email_change_otp', 10)->nullable()->comment('OTP for email change verification');
            $table->timestamp('email_change_otp_expires_at')->nullable()->comment('Email change OTP expiry');
            $table->string('mobile_change_otp', 10)->nullable()->comment('OTP for mobile change verification');
            $table->timestamp('mobile_change_otp_expires_at')->nullable()->comment('Mobile change OTP expiry');

            /** -------------------------------
             *  Address & Location
             * ------------------------------- */
            $table->string('address_line1', 255)->comment('Primary address line');
            $table->string('address_line2', 255)->nullable()->comment('Secondary address line');

            $table->unsignedBigInteger('city_id')->nullable()->comment('City reference');
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('SET NULL');

            $table->unsignedBigInteger('state_id')->nullable()->comment('State reference');
            $table->foreign('state_id')->references('id')->on('states')->onDelete('SET NULL');

            $table->unsignedBigInteger('country_id')->nullable()->comment('Country reference');
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('SET NULL');

            $table->string('postal_code', 10)->comment('Postal or ZIP code');

            /** -------------------------------
             *  Geographic Location
             * ------------------------------- */
            $table->decimal('latitude', 10, 7)->nullable()->comment('Branch latitude coordinate');
            $table->decimal('longitude', 10, 7)->nullable()->comment('Branch longitude coordinate');

            /** -------------------------------
             *  Tax & Compliance
             * ------------------------------- */
            $table->string('gstin', 255)->nullable()->unique()->comment('Branch GST Identification Number');
            $table->json('tax_details')->nullable()->comment('Tax details in JSON format, e.g., {"CGST": 9, "SGST": 9}');

            /** -------------------------------
             *  Branch Classification
             * ------------------------------- */
            $table->enum('type', ['head_office', 'regional', 'franchise', 'sub_branch'])
                ->default('sub_branch')
                ->comment('Defines the type of branch');

            /** -------------------------------
             *  Operating Hours
             * ------------------------------- */
            $table->json('operating_hours')->nullable()->comment('Store open-close timings, e.g., {"Monday": "9am-6pm", "Sunday": "Closed"}');

            /** -------------------------------
             *  SMTP Configuration
             * ------------------------------- */
            $table->string('smtp_host')->nullable()->comment('SMTP server host');
            $table->integer('smtp_port')->nullable()->comment('SMTP port number (e.g., 587, 465)');
            $table->string('smtp_username')->nullable()->comment('SMTP authentication username');
            $table->string('smtp_password')->nullable()->comment('SMTP authentication password (store securely)');
            $table->string('smtp_encryption', 10)->nullable()->comment('SMTP encryption type (e.g., tls, ssl)');
            $table->string('smtp_from_email')->nullable()->comment('Sender email for outgoing mail');
            $table->string('smtp_from_name')->nullable()->comment('Sender name for outgoing mail');
            $table->boolean('use_branch_smtp_credentials')->default(false)->comment('Flag to indicate use of branch-specific SMTP credentials');

            /** -------------------------------
             *  Social Media
             * ------------------------------- */
            $table->json('social_links')->nullable()->comment('Social media profiles in JSON format');

            /** -------------------------------
             *  Branch Status
             * ------------------------------- */
            $table->enum('status', ['active', 'inactive', 'suspended', 'archived'])
                ->default('active')
                ->comment('Branch current status');

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
            $table->index(['city_id', 'state_id', 'country_id']);
            $table->index(['branch_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_branches');
    }
};
