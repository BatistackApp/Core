<?php

namespace App\Filament\Resources\Tiers\Tiers;

use App\Filament\Resources\Tiers\Tiers\Pages\CreateTiers;
use App\Filament\Resources\Tiers\Tiers\Pages\EditTiers;
use App\Filament\Resources\Tiers\Tiers\Pages\ListTiers;
use App\Filament\Resources\Tiers\Tiers\Schemas\TiersForm;
use App\Filament\Resources\Tiers\Tiers\Tables\TiersTable;
use App\Models\Tiers\Tiers;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TiersResource extends Resource
{
    protected static ?string $model = Tiers::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Tiers';

    public static function form(Schema $schema): Schema
    {
        return TiersForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TiersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTiers::route('/'),
            'create' => CreateTiers::route('/create'),
            'edit' => EditTiers::route('/{record}/edit'),
        ];
    }
}
