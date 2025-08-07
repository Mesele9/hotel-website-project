<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('event_spaces', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->integer('capacity');
            $table->string('image')->nullable();
            // You could add more specific fields here later, like 'size_sqft', 'layout_options', etc.
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('event_spaces'); }
};