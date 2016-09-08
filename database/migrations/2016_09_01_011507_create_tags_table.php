<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTagsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    // @TODO this is shortsighted. storing _every_ tag in the system in a single
    // table is going to get out of hand. We'll need to shard this somehow
    // either by placing entire projects/cards/tags in separate databases
    // or by creating a project_tags table that only holds tags for the single
    // project
    //
    // What's worse, this would all be so much easier with a search engine like
    // elasticsearch or solr...
    Schema::create('tags', function (Blueprint $table) {
      $table->increments('id');
      $table->integer('project_id')->unsigned();
      $table->string('name');
      $table->timestamps();
    });

    Schema::create('card_tag', function (Blueprint $table) {
      $table->increments('id');
      $table->integer('card_id')->unsigned();
      $table->integer('tag_id')->unsigned();
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
    Schema::dropIfExists('tags');
    Schema::dropIfExists('card_tag');
  }
}
