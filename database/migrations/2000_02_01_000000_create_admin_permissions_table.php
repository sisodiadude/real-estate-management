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
        Schema::create('admin_permissions', function (Blueprint $table) {
            // Primary Key
            $table->id()->comment('Primary key of the admin_permissions table');

            // Permission Group Details
            $table->string('group', 100)->index()->comment('Identifier for the permission group or role');

            // Permission Attributes
            $table->string('action', 100)->comment('Specific action allowed for the group');
            $table->text('description')->nullable()->comment('Detailed description of the permission');

            // Status Flags
            $table->boolean('is_active')->default(true)->comment('Indicates whether the permission is active');

            // Timestamps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_permissions');
    }
};
