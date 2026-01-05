<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;

use Filament\Forms;
use Illuminate\Database\Eloquent\Collection;
use Spatie\Permission\Models\Role;
use Filament\Forms\Components\Select;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100, 'all'])
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                TextColumn::make('oni')
                    ->label('ONI')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Fecha de creacion')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Fecha de Actualizacion')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('roles.name')
                    ->label('Roles')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('roles')
                    ->label('Filtrar por Rol')
                    ->relationship('roles', 'name'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('asignar_rol')
                        ->label('Asignar Rol')
                        ->icon('heroicon-o-user-group')
                        ->form([
                           Select::make('role')
                                ->label('Rol')
                                ->options(function () {
                                    return Role::pluck('name', 'id');
                                })
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $role = Role::findById($data['role']);

                            foreach ($records as $record) {
                                $record->assignRole($role);
                            }
                        }),
                ]),
            ]);
    }
}
