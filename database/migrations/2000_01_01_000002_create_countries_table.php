<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id()->comment('Primary key of the countries table');
            $table->string('name')->comment('Name of the country');
            $table->char('iso3', 3)->nullable()->comment('ISO 3166-1 alpha-3 code of the country');
            $table->char('numeric_code', 3)->nullable()->comment('Numeric code for the country');
            $table->char('iso2', 2)->nullable()->comment('ISO 3166-1 alpha-2 code of the country');
            $table->string('phonecode')->nullable()->comment('Country dialing code');
            $table->string('capital')->nullable()->comment('Capital city of the country');
            $table->string('currency')->nullable()->comment('Currency code of the country');
            $table->string('currency_name')->nullable()->comment('Name of the currency used in the country');
            $table->string('currency_symbol')->nullable()->comment('Currency symbol used in the country');
            $table->string('tld')->nullable()->comment('Top-level domain (TLD) for the country');
            $table->string('native')->nullable()->comment('Native name of the country');
            $table->string('region')->nullable()->comment('Region where the country is located');
            $table->unsignedBigInteger('region_id')->nullable()->comment('Foreign key referencing the region the country belongs to');
            $table->foreign('region_id')->references('id')->on('regions')->onDelete('cascade')
                ->comment('Foreign key constraint for region_id, cascading deletes to ensure referential integrity');
            $table->string('sub_region')->nullable()->comment('Sub-region within the country, if applicable');
            $table->unsignedBigInteger('sub_region_id')->nullable()->comment('Foreign key referencing the sub-region the country belongs to');
            $table->foreign('sub_region_id')->references('id')->on('sub_regions')->onDelete('cascade')
                ->comment('Foreign key constraint for sub_region_id, cascading deletes to ensure referential integrity');
            $table->string('nationality')->nullable()->comment('Nationality of the country’s citizens');
            $table->text('timezones')->nullable()->comment('List of timezones in the country');
            $table->text('translations')->nullable()->comment('Translations for the country name and other details');
            $table->decimal('latitude', 10, 8)->nullable()->comment('Latitude coordinate of the country’s location');
            $table->decimal('longitude', 11, 8)->nullable()->comment('Longitude coordinate of the country’s location');
            $table->string('emoji')->nullable()->comment('Emoji flag representing the country');
            $table->string('emojiU')->nullable()->comment('Unicode representation of the country’s emoji flag');
            $table->tinyInteger('flag')->default(1)->comment('Flag indicating the status or condition of the country (default: 1)');
            $table->string('wikiDataId')->nullable()->comment('Rapid API GeoDB Cities WikiData ID for the country');
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
        Schema::dropIfExists('countries');
    }
}
