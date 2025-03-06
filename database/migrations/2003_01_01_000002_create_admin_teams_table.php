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
        Schema::create('admin_teams', function (Blueprint $table) {
            $table->id();

            /** -------------------------------
             *  Basic Information
             * ------------------------------- */
            $table->unsignedBigInteger('branch_id')->comment('Branch reference');
            $table->foreign('branch_id')->references('id')->on('admin_branches')->onDelete('CASCADE');

            $table->unsignedBigInteger('department_id')->comment('Department reference');
            $table->foreign('department_id')->references('id')->on('admin_departments')->onDelete('CASCADE');

            $table->string('team_unique_id')->unique()->comment('Unique ID of team');
            $table->string('name')->comment('Team name');
            $table->string('slug')->unique()->comment('SEO-friendly unique identifier');
            $table->text('description')->nullable()->comment('Brief team description');

            /** -------------------------------
             *  Leadership & Management
             * ------------------------------- */
            $table->foreignId('leader_id')->nullable()->comment('Team leader admin ID');

            /** -------------------------------
             *  Contact Information
             * ------------------------------- */
            $table->string('email', 100)->unique()->nullable()->comment('Team contact email');
            $table->timestamp('email_verified_at')->nullable()->comment('Email verification timestamp');
            $table->string('mobile', 15)->unique()->nullable()->comment('Team contact mobile number');
            $table->timestamp('mobile_verified_at')->nullable()->comment('Mobile verification timestamp');

            /** -------------------------------
             *  OTP Verification
             * ------------------------------- */
            $table->string('email_change_otp', 10)->nullable()->comment('OTP for email change verification');
            $table->timestamp('email_change_otp_expires_at')->nullable()->comment('Email change OTP expiry');
            $table->string('mobile_change_otp', 10)->nullable()->comment('OTP for mobile change verification');
            $table->timestamp('mobile_change_otp_expires_at')->nullable()->comment('Mobile change OTP expiry');

            /** -------------------------------
             *  Team Status
             * ------------------------------- */
            $table->enum('status', ['active', 'inactive', 'suspended', 'archived'])
                ->default('active')
                ->comment('Team current status');

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
            $table->index(['branch_id', 'department_id']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_teams');
    }
};
