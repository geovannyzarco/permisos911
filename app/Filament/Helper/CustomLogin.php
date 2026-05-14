<?php

namespace App\Filament\Helper;

use Filament\Auth\Pages\Login;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Validation\ValidationException;

class CustomLogin extends Login
{
    public function getAuthenticateFormAction(): \Filament\Actions\Action
    {
        return parent::getAuthenticateFormAction()
            ->label('Entrar');
    }

    public function getHeading(): \Illuminate\Contracts\Support\Htmlable|string
    {
        return 'Entre a su cuenta';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('oni')
                    ->label('ONI')
                    ->required()
                    ->maxLength(50)
                    ->placeholder('Ingrese su ONI'),
                $this->getPasswordFormComponent()
                    ->label('Contraseña'),
                $this->getRememberFormComponent()
                    ->label('Recordarme'),
            ]);
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'oni' => $data['oni'],
            'password' => $data['password'],
        ];
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.password' => 'Usuario o contraseña incorrectos.',
        ]);
    }

    public function logout()
    {
        auth()->logout();

        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('filament.auth.login');
    }
}
