<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Schemas\Schema;
use DiogoGPinto\AuthUIEnhancer\Pages\Auth\Concerns\HasCustomLayout;

class Login extends BaseLogin
{
    use HasCustomLayout;

    #[Override]
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getEmailFormComponent()
                    ->autofocus()
                    ->label('Email')
                    ->placeholder('your-name@example.com'),

                $this->getPasswordFormComponent()
                    ->revealable(),

                $this->getRememberFormComponent()
            ]);
    }
}
