<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('states', function (Blueprint $table) {
            $table->id()->comment('Primary key of the states table');
            $table->string('name')->comment('Name of the state');
            $table->unsignedBigInteger('country_id')->comment('Foreign key referencing the country the state belongs to');
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade')
                ->comment('Foreign key constraint for country_id, cascading deletes to ensure referential integrity');
            $table->char('country_code', 2)->comment('Country code for the state using ISO 3166-1 alpha-2 code');
            $table->string('fips_code')->nullable()->comment('FIPS code for the state (Federal Information Processing Standard)');
            $table->string('iso2')->nullable()->comment('ISO 3166-2 code for the state');
            $table->string('type')->nullable()->comment('Type of the state (e.g., province, region, etc.)');
            $table->decimal('latitude', 10, 8)->nullable()->comment('Latitude coordinate of the state’s location');
            $table->decimal('longitude', 11, 8)->nullable()->comment('Longitude coordinate of the state’s location');
            $table->tinyInteger('flag')->default(1)->comment('Flag indicating the status or condition of the state (default: 1)');
            $table->string('wikiDataId')->nullable()->comment('Rapid API GeoDB Cities WikiData ID for the state');
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
        Schema::dropIfExists('states');
    }
}
