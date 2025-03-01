<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubRegionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub_regions', function (Blueprint $table) {
            $table->id()->comment('Primary key of the sub_regions table');
            $table->string('name')->comment('Name of the sub-region');
            $table->text('translations')->nullable()->comment('Translations for the sub-region in different languages');
            $table->unsignedBigInteger('region_id')->comment('Foreign key referencing the region to which the sub-region belongs');
            $table->foreign('region_id')->references('id')->on('regions')->onDelete('cascade')
                ->comment('Foreign key constraint for region_id, cascading deletes to ensure referential integrity');
            $table->tinyInteger('flag')->default(1)->comment('Flag indicating the status or condition of the sub-region (default: 1)');
            $table->string('wikiDataId')->nullable()->comment('Rapid API GeoDB Cities WikiData ID for the sub-region');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sub_regions');
    }
}
