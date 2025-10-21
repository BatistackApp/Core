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
        Schema::create('tiers_supplies', function (Blueprint $table) {
            $table->id();
            $table->boolean('tva')->default(true);
            $table->string('num_tva')->nullable();
            $table->string('rem_relative')->nullable();
            $table->string('rem_fixe')->nullable();
            $table->foreignId('code_comptable_general')->constrained('plan_comptables');
            $table->foreignId('code_comptable_fournisseur')->constrained('plan_comptables');
            $table->foreignId('tiers_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('condition_reglement_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('mode_reglement_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tiers_supplies');
    }
};
