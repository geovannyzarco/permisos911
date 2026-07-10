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
        $oni = trim($data['oni']);
        $user = \App\Models\User::where('oni', $oni)->first();

        if (!$user && is_numeric($oni)) {
            $oniInt = (int)$oni;
            $user = \App\Models\User::where(function ($query) use ($oniInt) {
                $query->where('oni', (string)$oniInt)
                      ->orWhere('oni', sprintf('%02d', $oniInt))
                      ->orWhere('oni', sprintf('%03d', $oniInt))
                      ->orWhere('oni', sprintf('%04d', $oniInt))
                      ->orWhere('oni', sprintf('%05d', $oniInt))
                      ->orWhere('oni', sprintf('%06d', $oniInt));
            })->first();
        }

        return [
            'oni' => $user ? $user->oni : $oni,
            'password' => $data['password'],
        ];
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.password' => 'Usuario o contraseña incorrectos.',
        ]);
    }
}
