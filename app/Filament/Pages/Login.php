<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Schemas\Components\Form;


class Login extends Page
{
    protected string $view = 'filament.pages.login';
    protected function getForms(): array
{
return [
        'form'=>$this->form(
            Form::make()
                ->schema([
                    TextInput::make('oni')
                    ->label('ONI')
                    ->required()
                    ->autocomplete(false),

                    TextInput::make('password')
                    ->label('Contraseña')
                    ->password()
                    ->required(),

                ])
        ),
    ];

}

    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'oni' => $data['oni'],
            'password' => $data['password'],
        ];
    }

}





