<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionsTable extends Migration
{
  public function up()
  {
    Schema::create('questions', function (Blueprint $table) {
      $table->id();
      $table->foreignId('standard_section_id')->constrained()->onDelete('cascade');
      $table->text('question_text');
      $table->string('question_type'); // text, checkbox, radio, select
      $table->text('options')->nullable(); // JSON for select/radio options
      $table->boolean('is_required')->default(true);
      $table->integer('display_order');
      $table->timestamps();
    });
  }

  public function down()
  {
    Schema::dropIfExists('questions');
  }
}
