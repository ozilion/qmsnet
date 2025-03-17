<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStandardsTable extends Migration
{
  public function up()
  {
    Schema::create('standards', function (Blueprint $table) {
      $table->id();
      $table->string('code')->unique(); // ISO 9001, ISO 14001, etc.
      $table->string('name');
      $table->string('version'); // 2015, 2018, etc.
      $table->text('description')->nullable();
      $table->boolean('is_active')->default(true);
      $table->timestamps();
    });
  }

  public function down()
  {
    Schema::dropIfExists('standards');
  }
}
