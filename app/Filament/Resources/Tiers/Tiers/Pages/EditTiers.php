<?php

namespace App\Filament\Resources\Tiers\Tiers\Pages;

use App\Filament\Resources\Tiers\Tiers\TiersResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTiers extends EditRecord
{
    protected static string $resource = TiersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
