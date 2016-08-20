<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCardAssignmentsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('assignees', function (Blueprint $table) {
      $table->increments('id');
      $table->string('source_type');
      $table->integer('source_id')->unsigned();
      $table->integer('user_id')->unsigned();
      $table->string('link');
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
    Schema::dropIfExists('assignees');
  }
}
