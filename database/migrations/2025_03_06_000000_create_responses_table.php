<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResponsesTable extends Migration
{
  public function up()
  {
    Schema::create('responses', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->unique(); // For mobile sync
      $table->foreignId('audit_id')->constrained()->onDelete('cascade');
      $table->foreignId('question_id')->constrained();
      $table->text('response_text')->nullable();
      $table->boolean('is_compliant')->nullable();
      $table->text('evidence')->nullable(); // Evidence description or file paths
      $table->boolean('is_synced')->default(true);
      $table->timestamp('sync_timestamp')->nullable();
      $table->timestamps();
    });
  }

  public function down()
  {
    Schema::dropIfExists('responses');
  }
}
