<?php

namespace App\Filament\Resources\Tiers\Tiers\Pages;

use App\Filament\Resources\Tiers\Tiers\TiersResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTiers extends ListRecords
{
    protected static string $resource = TiersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
