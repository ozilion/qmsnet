<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditsTable extends Migration
{
  public function up()
  {
    Schema::create('audits', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->unique(); // For mobile sync
      $table->foreignId('user_id')->constrained();
      $table->foreignId('standard_revision_id')->constrained();
      $table->foreignId('plan_no')->nullable(); // Link to existing planlar table
      $table->string('audit_type'); // Initial, Surveillance, Recertification
      $table->string('company_name');
      $table->date('audit_date');
      $table->string('status'); // draft, in_progress, completed, approved
      $table->timestamps();
    });
  }

  public function down()
  {
    Schema::dropIfExists('audits');
  }
}
