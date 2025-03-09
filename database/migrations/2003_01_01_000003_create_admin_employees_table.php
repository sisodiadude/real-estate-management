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
        Schema::create('admin_employees', function (Blueprint $table) {
            $table->id();

            /** -------------------------------
             *  Basic Information
             * ------------------------------- */
            $table->unsignedBigInteger('branch_id')->comment('Branch reference');
            $table->foreign('branch_id')->references('id')->on('admin_branches')->onDelete('CASCADE');

            $table->unsignedBigInteger('department_id')->comment('Department reference');
            $table->foreign('department_id')->references('id')->on('admin_departments')->onDelete('CASCADE');

            $table->unsignedBigInteger('team_id')->comment('Team reference');
            $table->foreign('team_id')->references('id')->on('admin_teams')->onDelete('CASCADE');

            $table->string('employee_unique_id', 50)->unique()->comment('Unique employee ID');
            $table->string('first_name', 50)->comment('Admin first name');
            $table->string('last_name', 50)->nullable()->comment('Admin last name');
            $table->string('designation', 100)->comment('Job designation');
            $table->date('date_of_birth')->comment('Date of birth');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->comment('Gender');
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widow'])->nullable()->comment('Marital status');
            $table->string('profile_picture', 255)->nullable()->comment('URL of the profile picture');
            $table->string('resume', 255)->nullable()->comment('URL of the resume or CV document');
            $table->string('govt_id', 255)->nullable()->comment('URL of the government-issued ID');
            $table->string('education_certificates', 255)->nullable()->comment('URL of education-related certificates');
            $table->foreignId('nationality_id')->nullable()->constrained('countries')->nullOnDelete()->comment('Nationality (Country reference)');
            $table->enum('blood_group', ['A', 'A-', 'A+', 'B', 'B-', 'B+', 'O', 'O-', 'O+', 'AB', 'AB-', 'AB+'])->nullable()->comment('Blood group');

            /** -------------------------------
             *  Employment Details
             * ------------------------------- */
            $table->date('joining_date')->nullable()->comment('Joining date');
            $table->unsignedMediumInteger('probation_period')->nullable()->comment('Probation period in months');
            $table->enum('employment_type', ['full_time', 'part_time', 'contract'])->default('full_time')->comment('Employment type');
            $table->unsignedMediumInteger('salary')->nullable()->comment('Salary amount');
            $table->string('bank_account', 20)->nullable()->comment('Bank account number');
            $table->string('bank_name', 50)->nullable()->comment('Bank name');

            /** -------------------------------
             *  Contact Information
             * ------------------------------- */
            $table->string('email', 100)->unique()->comment('Primary email');
            $table->timestamp('email_verified_at')->nullable()->comment('Email verification timestamp');
            $table->string('alternative_email', 100)->nullable()->comment('Alternative email');
            $table->string('mobile', 15)->unique()->comment('Primary mobile number');
            $table->timestamp('mobile_verified_at')->nullable()->comment('Mobile verification timestamp');
            $table->string('alternate_mobile', 15)->nullable()->comment('Alternative mobile number');

            /** -------------------------------
             *  OTP Verification
             * ------------------------------- */
            $table->string('email_change_otp', 10)->nullable()->comment('OTP for email change');
            $table->timestamp('email_change_otp_expires_at')->nullable()->comment('Email change OTP expiry');
            $table->unsignedMediumInteger('email_verification_otp')->nullable()->comment('Email verification OTP');
            $table->timestamp('email_verification_otp_expires_at')->nullable()->comment('Email verification OTP expiry');
            $table->string('mobile_change_otp', 10)->nullable()->comment('OTP for mobile change');
            $table->timestamp('mobile_change_otp_expires_at')->nullable()->comment('Mobile change OTP expiry');
            $table->unsignedMediumInteger('mobile_verification_otp')->nullable()->comment('Mobile verification OTP');
            $table->timestamp('mobile_verification_otp_expires_at')->nullable()->comment('Mobile verification OTP expiry');

            /** -------------------------------
             *  Address Information
             * ------------------------------- */
            // Current Address
            $table->string('current_address_line1', 255)->nullable()->comment('Current address line 1');
            $table->string('current_address_line2', 255)->nullable()->comment('Current address line 2');
            $table->foreignId('current_city_id')->nullable()->constrained('cities')->nullOnDelete()->comment('Current city');
            $table->foreignId('current_state_id')->nullable()->constrained('states')->nullOnDelete()->comment('Current state');
            $table->foreignId('current_country_id')->nullable()->constrained('countries')->nullOnDelete()->comment('Current country');
            $table->string('current_postal_code', 10)->nullable()->comment('Current postal/ZIP code');
            $table->boolean('same_as_current_address')->default(false)->comment('Is permanent address same as current');

            // Permanent Address
            $table->string('permanent_address_line1', 255)->nullable()->comment('Permanent address line 1');
            $table->string('permanent_address_line2', 255)->nullable()->comment('Permanent address line 2');
            $table->foreignId('permanent_city_id')->nullable()->constrained('cities')->nullOnDelete()->comment('Permanent city');
            $table->foreignId('permanent_state_id')->nullable()->constrained('states')->nullOnDelete()->comment('Permanent state');
            $table->foreignId('permanent_country_id')->nullable()->constrained('countries')->nullOnDelete()->comment('Permanent country');
            $table->string('permanent_postal_code', 10)->nullable()->comment('Permanent postal/ZIP code');

            /** -------------------------------
             *  Account Management
             * ------------------------------- */
            $table->enum('account_status', ['active', 'inactive', 'suspended'])->default('active')->comment('Account status');
            $table->timestamp('account_locked_until')->nullable()->comment('Account lock expiration');
            $table->timestamp('password_updated_at')->nullable()->comment('Last password update timestamp');
            $table->json('notification_preferences')->nullable()->comment('Notification preferences (JSON)');

            /** -------------------------------
             *  Banking & Tax Information
             * ------------------------------- */
            $table->string('ifsc_swift_code', 50)->nullable()->comment('IFSC or SWIFT Code');
            $table->string('pan_tax_id', 50)->nullable()->comment('PAN or Tax ID');

            /** -------------------------------
             *  Salary & Compensation
             * ------------------------------- */
            $table->enum('salary_frequency', ['daily', 'weekly', 'biweekly', 'monthly', 'yearly'])->default('monthly')->comment('Salary payment frequency');
            $table->json('allowances')->nullable()->comment('Allowances JSON data');
            $table->json('deductions')->nullable()->comment('Deductions JSON data');

            /** -------------------------------
             *  Emergency Contact
             * ------------------------------- */
            $table->string('emergency_contact_name', 100)->nullable()->comment('Emergency contact person');
            $table->string('emergency_contact_relation', 50)->nullable()->comment('Relation with emergency contact');
            $table->string('emergency_contact_number', 15)->nullable()->comment('Emergency contact phone number');

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
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_employees');
    }
};
