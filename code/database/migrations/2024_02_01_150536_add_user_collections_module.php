<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserCollectionsModule extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('collections', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('owner_id');
            $table->string('owner_type');

            $table->string('name')->nullable();
            $table->boolean('is_public')->default(true);

            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('collection_items', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('item_id');
            $table->string('item_type');

            $table->unsignedInteger('collection_id');
            $table->foreign('collection_id')->references('id')->on('collections');

            $table->unsignedInteger('order');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('collection_items');
        Schema::dropIfExists('collections');
    }
}
