<?php

namespace App\Filament\Resources\GestionPermisos\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\FileColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class CompensadosRelationManager extends RelationManager
{
    protected static string $relationship = 'compensados';

    /**
     * Mostrar solo si el permiso es compensado
     */
public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
{
    return (int) $ownerRecord->tipo_permiso_id === 2;
}

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            DateTimePicker::make('desde')
                ->label('Desde')
                ->native(false)
                ->displayFormat('d/m/Y H:i')
                ->format('Y-m-d H:i')
                ->required(),

            DateTimePicker::make('hasta')
                ->label('Hasta')
                ->native(false)
                ->displayFormat('d/m/Y H:i')
                ->format('Y-m-d H:i')
                ->required()

                ->rules([
                    function () {
                        return function (string $attribute, $value, $fail){
                            $permiso = $this->getOwnerRecord();
                            $desde = request()->input('mountedActionsData.0.desde');
                            if(
                                $desde < $permiso->desde ||
                                $value > $permiso->hasta

                            ){
                                $fail('El rango de fechas del compensado debe estar dentro del rango del permiso.');
                            }
                        };
                    }
                ]),

            TextInput::make('justificacion')
                ->label('Justificación')
                ->required()
                ->maxLength(255),

            FileUpload::make('adjunto')
                ->label('Adjunto')
                ->disk('public')
                ->directory('compensados/adjuntos')
                ->preserveFilenames()
                ->downloadable()
                ->nullable(),

        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('justificacion')
            ->columns([
                TextColumn::make('desde')
                    ->label('Desde')
                    ->dateTime('d/m/Y H:i'),

                TextColumn::make('hasta')
                    ->label('Hasta')
                    ->dateTime('d/m/Y H:i'),

                TextColumn::make('justificacion')
                    ->label('Justificación')
                    ->limit(40),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->since(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }
}
