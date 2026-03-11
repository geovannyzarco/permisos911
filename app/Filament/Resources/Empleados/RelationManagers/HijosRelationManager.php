<?php

namespace App\Filament\Resources\Empleados\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;

class HijosRelationManager extends RelationManager
{
    protected static string $relationship = 'hijos';
    protected static ?string $title = 'Hijos';
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
            TextInput::make('nombre')
                ->required()
                ->maxLength(150),

            DatePicker::make('fecha_nacimiento')
                ->required()
                ->native(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('hijo')
            ->columns([
                TextColumn::make('nombre')
                    ->searchable(),

                TextColumn::make('fecha_nacimiento')
                    ->label('Fecha nacimiento')
                    ->date(),

                TextColumn::make('edad')
                    ->label('Edad')
                    ->getStateUsing(
                        fn ($record) => Carbon::parse($record->fecha_nacimiento)->age . ' años'
                    ),
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
