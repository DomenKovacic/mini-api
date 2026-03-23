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
        Schema::create('tasks', function (Blueprint $table) {
            //Task polja
        $table->id(); //primary key
        $table->string('title', 255); // task title
        $table->text('description')->nullable(); // optional daljsi text
        $table->string('status')->default('todo'); //shranjeno kot string - todo, in_progress,done,failed
        $table->string('priority')->default('medium'); // shranjeno kot string - low, medium, high
        $table->date('due_date')->nullable(); //optional datum
        $table->string('external_reference', 100)->nullable()->unique(); // optional unique string
        $table->json('metadata')->nullable(); //json payload
        $table->timestamps(); //created_at in updated_at
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
