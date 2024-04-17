<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProviderServicesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('provider_service', function (Blueprint $table) {

      $table->unsignedBigInteger('provider_id');
      $table->unsignedBigInteger('service_id');
      $table->integer('subscribe')->default(0);
      $table->timestamps();

      $table->foreign('provider_id')->references('id')->on('users')->onDelete('cascade');
      $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('provider_service');
  }
}
