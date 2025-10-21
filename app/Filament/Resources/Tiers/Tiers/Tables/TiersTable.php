<?php

namespace App\Filament\Resources\Tiers\Tiers\Tables;

use App\Enums\Tiers\TiersType;
use App\Models\Tiers\Tiers;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TiersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(Tiers::with('addresse', 'contacts', 'supply', 'customer'))
            ->columns([
                TextColumn::make('id')
                    ->label("#")
                    ->sortable(),

                TextColumn::make('name')
                    ->label("Identité (Raison Social)")
                    ->searchable(),

                TextColumn::make('nature')
                    ->label('Nature')
                    ->formatStateUsing(fn (string $state): string => TiersType::from($state)->label()),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
