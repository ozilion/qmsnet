<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommonSectionsTable extends Migration
{
  public function up()
  {
    Schema::create('common_sections', function (Blueprint $table) {
      $table->id();
      $table->foreignId('standard_revision_id')->constrained()->onDelete('cascade');
      $table->string('section_title');
      $table->string('section_type'); // header, footer, signature area, etc.
      $table->text('content')->nullable();
      $table->integer('display_order');
      $table->timestamps();
    });
  }

  public function down()
  {
    Schema::dropIfExists('common_sections');
  }
}
