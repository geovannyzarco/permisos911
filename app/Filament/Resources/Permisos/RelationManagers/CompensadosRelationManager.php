<?php

namespace App\Filament\Resources\Permisos\RelationManagers;

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

class CompensadosRelationManager extends RelationManager
{
    protected static string $relationship = 'compensados';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord->tipo_permiso_id == 2;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DateTimePicker::make('desde')
                    ->label('Desde')
                    ->displayFormat('d/m/Y H:i')
                    ->format('Y-m-d H:i')
                    ->withoutSeconds()
                    ->native(false)
                    ->required(),
                DateTimePicker::make('hasta')
                    ->label('Hasta')
                    ->displayFormat('d/m/Y H:i')
                    ->format('Y-m-d H:i')
                    ->withoutSeconds()
                    ->native(false)
                    ->required(),
                Textarea::make('justificacion')
                    ->label('Justificación')
                    ->required()
                    ->maxLength(65535)
                    ->columnSpanFull(),

                FileUpload::make('adjunto')
                    ->label('Adjunto')

                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('justificacion')
            ->columns([
                TextColumn::make('desde')
                    ->dateTime('Y-m-d H:i')
                    ->searchable(),
                TextColumn::make('hasta')
                    ->dateTime('Y-m-d H:i')
                    ->searchable(),
                TextColumn::make('justificacion')
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('adjunto')
                    ->label('Adjunto')
                    ->getStateUsing(function ($record): string {
                        $url = Storage::url($record->adjunto);
                        $url = basename($record->adjunto);
                        return "<a href='{$url}' target='_blank'>Ver Adjunto</a>";
                    })
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
                //AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DissociateAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
