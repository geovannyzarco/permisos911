<?php

namespace App\Filament\Resources\Permisos\RelationManagers;

use App\Filament\Resources\Compensados\CompensadoResource;
use App\Filament\Resources\Permisos\PermisosResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class CompensadosRelationManager extends RelationManager
{
    protected static string $relationship = 'Compensados';
    protected static ?string $title = 'Compensados';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord->tipo_permiso_id ==2;
    }

    protected static ?string $relatedResource = CompensadoResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make()
            ]);
    }
}
