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
        Schema::create('admins', function (Blueprint $table) {
            $table->id();

            /** -------------------------------
             *  Basic Information
             * ------------------------------- */
            $table->string('username', 50)->unique()->comment('Unique username for admin');
            $table->string('first_name', 50)->comment('Admin first name');
            $table->string('last_name', 50)->comment('Admin last name');
            $table->string('designation', 100)->comment('Admin job designation');
            $table->date('date_of_birth')->nullable()->comment('Date of birth');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->comment('Gender');
            $table->string('profile_picture', 255)->nullable()->comment('Profile picture URL');

            /** -------------------------------
             *  Contact Information
             * ------------------------------- */
            $table->string('email', 100)->unique()->comment('Admin contact email');
            $table->timestamp('email_verified_at')->nullable()->comment('Email verification timestamp');
            $table->string('mobile', 15)->unique()->comment('Admin mobile number');
            $table->timestamp('mobile_verified_at')->nullable()->comment('Mobile verification timestamp');

            /** -------------------------------
             *  OTP Verification
             * ------------------------------- */
            $table->string('email_change_otp', 10)->nullable()->comment('OTP for email change verification');
            $table->timestamp('email_change_otp_expires_at')->nullable()->comment('Email change OTP expiry timestamp');
            $table->unsignedMediumInteger('email_verification_otp')->nullable()->comment('OTP for email verification');
            $table->timestamp('email_verification_otp_expires_at')->nullable()->comment('Email verification OTP expiry timestamp');
            $table->string('mobile_change_otp', 10)->nullable()->comment('OTP for mobile change verification');
            $table->timestamp('mobile_change_otp_expires_at')->nullable()->comment('Mobile change OTP expiry timestamp');
            $table->unsignedMediumInteger('mobile_verification_otp')->nullable()->comment('OTP for mobile verification');
            $table->timestamp('mobile_verification_otp_expires_at')->nullable()->comment('Mobile verification OTP expiry timestamp');

            /** -------------------------------
             *  Address & Location
             * ------------------------------- */
            $table->string('address_line1', 255)->nullable()->comment('Primary address line');
            $table->string('address_line2', 255)->nullable()->comment('Secondary address line');
            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete()->comment('City reference');
            $table->foreignId('state_id')->nullable()->constrained('states')->nullOnDelete()->comment('State reference');
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete()->comment('Country reference');
            $table->string('postal_code', 10)->nullable()->comment('Postal or ZIP code');

            /** -------------------------------
             *  Authentication & Security
             * ------------------------------- */
            $table->string('password')->comment('Hashed password');
            $table->boolean('is_verified')->default(false)->comment('Account verification status');
            $table->boolean('is_two_factor_enabled')->default(false)->comment('Two-factor authentication status');
            $table->string('login_otp', 10)->nullable()->comment('OTP for login verification');
            $table->timestamp('login_otp_expires_at')->nullable()->comment('Login OTP expiry timestamp');
            $table->string('password_reset_otp', 10)->nullable()->comment('OTP for password reset');
            $table->timestamp('password_reset_otp_expires_at')->nullable()->comment('Password reset OTP expiry timestamp');
            $table->string('token', 255)->nullable()->comment('Authentication token');
            $table->timestamp('token_expires_at')->nullable()->comment('Token expiry timestamp');

            /** -------------------------------
             *  Account Management
             * ------------------------------- */
            $table->enum('account_status', ['active', 'inactive', 'suspended'])->default('active')->comment('Current account status');
            $table->timestamp('account_locked_until')->nullable()->comment('Timestamp until account is locked');
            $table->timestamp('password_updated_at')->nullable()->comment('Last password update timestamp');
            $table->json('notification_preferences')->nullable()->comment('Notification preferences in JSON format');

            /** -------------------------------
             *  Audit Trail (Created & Updated by)
             * ------------------------------- */
            $table->foreignId('created_by_id')->nullable()->constrained('admins')->nullOnDelete()->comment('ID of the creator admin');
            $table->foreignId('updated_by_id')->nullable()->constrained('admins')->nullOnDelete()->comment('ID of the last updater admin');

            /** -------------------------------
             *  Social Media
             * ------------------------------- */
            $table->json('social_links')->nullable()->comment('Social media profiles in JSON format');

            /** -------------------------------
             *  System Fields
             * ------------------------------- */
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
