<?php

namespace App\Filament\Resources\Locals\Pages;

use App\Filament\Resources\Locals\LocalResource;
use Filament\Resources\Pages\EditRecord;

class EditLocal extends EditRecord
{
    protected static string $resource = LocalResource::class;
}