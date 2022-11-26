<?php

namespace Filament\Pages\Auth;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Facades\Filament;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Notifications\Notification;
use Filament\Pages\CardPage;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\ValidationException;

/**
 * @property Form $form
 */
class Login extends CardPage
{
    use WithRateLimiting;

    /**
     * @var view-string $view
     */
    protected static string $view = 'filament::pages.auth.login';

    public $email = '';

    public $password = '';

    public $remember = false;

    public function mount(): void
    {
        if (Filament::auth()->check()) {
            redirect()->intended(Filament::getUrl());
        }

        $this->form->fill();
    }

    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            Notification::make()
                ->title(__('filament::pages/auth/login.messages.throttled', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]))
                ->danger()
                ->send();

            return null;
        }

        $data = $this->form->getState();

        if (! Filament::auth()->attempt([
            'email' => $data['email'],
            'password' => $data['password'],
        ], $data['remember'])) {
            throw ValidationException::withMessages([
                'email' => __('filament::pages/auth/login.messages.failed'),
            ]);
        }

        session()->regenerate();

        return app(LoginResponse::class);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('email')
                    ->label(__('filament::pages/auth/login.fields.email.label'))
                    ->email()
                    ->required()
                    ->autocomplete(),
                TextInput::make('password')
                    ->label(__('filament::pages/auth/login.fields.password.label'))
                    ->hint(filament()->hasPasswordReset() ? new HtmlString(Blade::render('<x-filament::link :href="filament()->getRequestPasswordResetUrl()"> {{ __(\'filament::pages/auth/login.buttons.request_password_reset.label\') }}</x-filament::link>')) : null)
                    ->password()
                    ->required(),
                Checkbox::make('remember')
                    ->label(__('filament::pages/auth/login.fields.remember.label')),
            ]);
    }

    public static function getName(): string
    {
        return 'filament.core.auth.login';
    }

    public function getTitle(): string
    {
        return __('filament::pages/auth/login.title');
    }

    public function getHeading(): string | Htmlable
    {
        return __('filament::pages/auth/login.heading');
    }
}
