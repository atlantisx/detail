<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;


class CreateContextsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('contexts', function(Blueprint $table){
            $table->increments('id');
            $table->integer('detail_id');
            $table->string('name',255);
            $table->string('condition_type',255);
            $table->string('condition_provider',255);
            $table->text('condition_parameters');
            $table->string('reaction_type',255);
            $table->string('reaction_provider',255);
            $table->text('reaction_parameters');
            $table->tinyInteger('status');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('contexts');
	}

}
