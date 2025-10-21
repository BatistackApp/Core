<?php

use App\Models\Core\Bank;
use App\Models\Tiers\Tiers;
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
        Schema::create('tiers_banks', function (Blueprint $table) {
            $table->id();
            $table->string('iban');
            $table->string('bic');
            $table->string('external_id')->nullable();
            $table->foreignIdFor(Tiers::class)->constrained('tiers');
            $table->foreignIdFor(Bank::class)->constrained('banks');
            $table->boolean('default');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tiers_banks');
    }
};
