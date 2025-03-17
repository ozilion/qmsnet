<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStandardSectionsTable extends Migration
{
  public function up()
  {
    Schema::create('standard_sections', function (Blueprint $table) {
      $table->id();
      $table->foreignId('standard_revision_id')->constrained()->onDelete('cascade');
      $table->string('clause_number'); // e.g., "4.1", "5.2.1"
      $table->string('clause_title');
      $table->text('description')->nullable();
      $table->integer('display_order');
      $table->boolean('is_mandatory')->default(true);
      $table->timestamps();
    });
  }

  public function down()
  {
    Schema::dropIfExists('standard_sections');
  }
}
