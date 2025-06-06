<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('plan_taxes', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100);
            $table->string('type', 100)->nullable()->default('percent')->comment('fixed , percent');
            $table->double('value')->nullable();
            $table->string('plan_ids')->nullable();
            $table->boolean('status')->nullable()->default(1)->comment('1 - Active , 0 - InActive');

            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->integer('deleted_by')->unsigned()->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_taxes');
    }
};
