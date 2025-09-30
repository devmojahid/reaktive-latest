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
            $table->foreignId('menu_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('url')->nullable();
            $table->string('target')->default('_self');
            $table->string('icon_class')->nullable();
            $table->integer('parent_id')->default(0);
            $table->integer('order')->default(0);
            $table->string('type')->default('custom'); // custom, page, category, etc.
            $table->integer('type_id')->nullable();
            $table->text('css_class')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
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
