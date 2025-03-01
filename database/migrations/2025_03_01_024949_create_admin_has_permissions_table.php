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
        Schema::create('admin_has_permissions', function (Blueprint $table) {
            $table->id()->comment('Primary key for the admin permission record');

            // Foreign key referencing the permission
            $table->foreignId('permission_id')
                ->constrained('admin_permissions')
                ->cascadeOnDelete()
                ->comment('Permission ID assigned to an admin');

            // Foreign key referencing the admin user
            $table->foreignId('admin_id')
                ->constrained('admins')
                ->cascadeOnDelete()
                ->comment('Admin ID associated with the permission');

            // Audit trail
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('admins')
                ->nullOnDelete()
                ->comment('Admin ID of the record creator');

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('admins')
                ->nullOnDelete()
                ->comment('Admin ID of the last updater');

            // Timestamps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_has_permissions');
    }
};
