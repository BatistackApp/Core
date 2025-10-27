<?php

namespace App\Livewire\Tiers;

use App\Models\Tiers\Tiers;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Liste des Tiers')]
#[Layout('components.layouts.tiers')]
class TiersList extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions, InteractsWithSchemas, InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Tiers::query())
            ->columns([
                TextColumn::make('name')
                    ->label('Identité')
                    ->searchable(),

                TextColumn::make('nature')
                    ->label("Nature du Tiers")
                    ->formatStateUsing(function (string $state): string {
                        return match ($state) {
                            'client' => 'Client',
                            'fournisseur' => 'Fournisseur',
                            default => 'Inconnu',
                        };
                    }),
            ]);
    }

    public function render()
    {
        return view('livewire.tiers.tiers-list');
    }
}
