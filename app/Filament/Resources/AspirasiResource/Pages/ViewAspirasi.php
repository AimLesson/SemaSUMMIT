<?php

namespace App\Filament\Resources\AspirasiResource\Pages;

use App\Filament\Resources\AspirasiResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAspirasi extends ViewRecord
{
    protected static string $resource = AspirasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
