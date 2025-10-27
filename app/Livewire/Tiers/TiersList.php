<?php

namespace App\Livewire\Tiers;

use App\Enums\Tiers\TiersNature;
use App\Models\Tiers\Tiers;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
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
                    ->badge()
                    ->color(fn (string $state): string => TiersNature::from($state)->color())
                    ->formatStateUsing(function (string $state): string {
                        return match ($state) {
                            'client' => 'Client',
                            'fournisseur' => 'Fournisseur',
                            default => 'Inconnu',
                        };
                    }),

                TextColumn::make('type')
                    ->label("Type de Tiers")
                    ->formatStateUsing(function (?Model $record): string {
                        return $record->type->value;
                    }),
                    
                TextColumn::make('address')
                    ->label("Adresse")
                    ->formatStateUsing(function (?Model $record): string {
                        return $record->address->first()->address."<br>".$record->address->first()->city." ".$record->address->first()->postal_code;
                    }),

            ])
            ->headerActions([
                Action::make('create_url')
                    ->label('Créer un tier')
                    ->color('primary')
                    ->url("#"),    
            ])
            ->recordActions([
                Action::make('edit_url')
                    ->label('Modifier')
                    ->color('primary')
                    ->url("#"),

                DeleteAction::make('delete')    
                    ->label('Supprimer')
                    ->color('danger')
                    ->requiresConfirmation(),
            ]);
    }

    public function render()
    {
        return view('livewire.tiers.tiers-list');
    }
}
