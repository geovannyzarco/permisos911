<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\CheckboxList;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('oni')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('password')
                    ->password()
                    ->required()
                    ->dehydrateStateUsing(fn ($state)=>filled($state) ? bcrypt($state): null)
                     ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => $context === 'create')
                    ->maxLength(255)
                    ->rule('nullable')
                    ->placeholder('Deja en blanco si no deseas cambiarla'),
<<<<<<< HEAD
                Select::make('roles')
                ->relationship('roles', 'name')
                ->multiple()
                ->preload()
                ->searchable(),

                // Using CheckboxList Component
               CheckboxList::make('roles')
                    ->relationship('roles', 'name')
                    ->searchable(),
=======




>>>>>>> a04db34d30b59592265ed2c0053ceff613b0dfe0
                            ]);
    }
}
