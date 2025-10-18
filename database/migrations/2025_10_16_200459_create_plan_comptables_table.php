<?php

declare(strict_types=1);

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
        Schema::create('plan_comptables', function (Blueprint $table): void {
            $table->id();
            $table->string('code')->nullable();
            $table->string('account')->nullable();
            $table->string('type')->nullable();
            $table->boolean('lettrage');
            $table->string('principal')->nullable();
            $table->float('initial')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_comptables');
    }
};
