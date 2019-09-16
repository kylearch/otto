<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('calendar_id');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->timestampTz('start')->nullable();
            $table->timestampTz('end')->nullable();
            $table->boolean('is_all_day')->default(false);
            $table->boolean('is_recurring')->default(false);
            $table->json('meta')->nullable();
            $table->timestampsTz();
            $table->softDeletesTz();

            $table->foreign('calendar_id')->references('id')->on('calendars')->onDelete('cascade');
        });

        Schema::create('recurrences', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('event_id');
            $table->unsignedSmallInteger('type');
            $table->unsignedSmallInteger('separation')->default(0);
            $table->unsignedSmallInteger('occurrences')->nullable();
            $table->unsignedTinyInteger('day_of_week')->nullable();
            $table->tinyInteger('day_of_month')->nullable();
            $table->tinyInteger('week_of_month')->nullable();
            $table->unsignedTinyInteger('month_of_year')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recurrences');
        Schema::dropIfExists('events');
    }
}
