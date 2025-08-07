<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::create('transactions', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->enum('type', ['income', 'expense']);
        $table->decimal('amount', 15, 2); // gunakan decimal, bukan unsignedBigInteger
        $table->unsignedBigInteger('category_id');
        $table->unsignedBigInteger('user_id');
        $table->timestamps();
    });

}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
