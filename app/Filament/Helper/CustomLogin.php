<?php

namespace App\Filament\Helper;

use Filament\Auth\Pages\Login;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CustomLogin extends Login
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('oni')
                    ->label('ONI')
                    ->required()
                    ->maxLength(50)
                    ->placeholder('Ingrese su ONI'),
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
            ]);
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'oni' => $data['oni'],
            'password' => $data['password'],
        ];
    }
}
