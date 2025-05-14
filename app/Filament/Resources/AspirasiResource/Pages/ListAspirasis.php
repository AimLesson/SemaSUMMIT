<?php

namespace App\Filament\Resources\AspirasiResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\AspirasiResource;

class ListAspirasis extends ListRecords
{
    protected static string $resource = AspirasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTableQuery(): Builder
    {
        if (auth()->user()->hasRole('super_admin')) {
            return parent::getTableQuery();
        }

        return parent::getTableQuery()->where('id_user', auth()->id());
    }
}
