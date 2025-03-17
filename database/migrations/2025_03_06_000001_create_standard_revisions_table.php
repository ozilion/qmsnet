<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStandardRevisionsTable extends Migration
{
  public function up()
  {
    Schema::create('standard_revisions', function (Blueprint $table) {
      $table->id();
      $table->foreignId('standard_id')->constrained()->onDelete('cascade');
      $table->string('revision_number');
      $table->date('revision_date');
      $table->text('revision_notes')->nullable();
      $table->string('docx_file_path');
      $table->boolean('is_current')->default(false);
      $table->timestamps();
    });
  }

  public function down()
  {
    Schema::dropIfExists('standard_revisions');
  }
}
