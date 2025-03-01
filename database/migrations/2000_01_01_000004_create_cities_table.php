<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->id()->comment('Primary key of the cities table');
            $table->string('name')->comment('Name of the city');
            $table->unsignedBigInteger('state_id')->comment('Foreign key referencing the state the city belongs to');
            $table->foreign('state_id')->references('id')->on('states')->onDelete('cascade')
                ->comment('Foreign key constraint for state_id, cascading deletes to ensure referential integrity');
            $table->string('state_code')->comment('State code, typically used to identify the city within the state');
            $table->unsignedBigInteger('country_id')->comment('Foreign key referencing the country the city belongs to');
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade')
                ->comment('Foreign key constraint for country_id, cascading deletes to ensure referential integrity');
            $table->char('country_code', 2)->comment('Country code for the city using ISO 3166-1 alpha-2 code');
            $table->decimal('latitude', 10, 8)->comment('Latitude coordinate of the city’s location');
            $table->decimal('longitude', 11, 8)->comment('Longitude coordinate of the city’s location');
            $table->tinyInteger('flag')->default(1)->comment('Flag indicating the status or condition of the city (default: 1)');
            $table->string('wikiDataId')->nullable()->comment('Rapid API GeoDB Cities WikiData ID for the city');
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
        Schema::dropIfExists('cities');
    }
}
