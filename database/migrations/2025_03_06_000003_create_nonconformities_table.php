<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNonconformitiesTable extends Migration
{
  public function up()
  {
    Schema::create('nonconformities', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->unique(); // For mobile sync
      $table->foreignId('audit_id')->constrained()->onDelete('cascade');
      $table->foreignId('standard_section_id')->constrained();
      $table->text('description');
      $table->string('severity'); // major, minor
      $table->text('correction')->nullable();
      $table->text('corrective_action')->nullable();
      $table->date('due_date')->nullable();
      $table->string('status'); // open, closed
      $table->boolean('is_synced')->default(true);
      $table->timestamp('sync_timestamp')->nullable();
      $table->timestamps();
    });
  }

  public function down()
  {
    Schema::dropIfExists('nonconformities');
  }
}
