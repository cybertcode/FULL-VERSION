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
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained()->cascadeOnDelete();
            $table->string('label');
            $table->string('type')->default('url'); // url | page | route
            $table->string('url')->nullable();
            $table->unsignedBigInteger('page_id')->nullable();
            $table->string('route_name')->nullable();
            $table->string('icon')->nullable();
            $table->string('target')->default('_self'); // _self | _blank
            $table->boolean('is_active')->default(true);
            $table->nestedSet();
            $table->timestamps();

            $table->index(['menu_id', 'parent_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
