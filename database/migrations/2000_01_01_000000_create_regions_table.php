<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('regions', function (Blueprint $table) {
            $table->id()->comment('Primary key of the regions table');
            $table->string('name')->comment('Name of the region');
            $table->text('translations')->nullable()->comment('Translations for the region in different languages');
            $table->tinyInteger('flag')->default(1)->comment('Flag indicating the status or condition of the region (default: 1)');
            $table->string('wikiDataId')->nullable()->comment('Rapid API GeoDB Cities WikiData ID for the region');
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
        Schema::dropIfExists('regions');
    }
}
