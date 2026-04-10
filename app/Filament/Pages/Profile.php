<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class Profile extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationGroup = 'System Management';
    protected static ?string $navigationLabel = 'My Profile';
    protected static ?int $navigationSort = 999;
    protected static string $view = 'filament.pages.profile';
    protected static ?string $title = 'My Profile';

    public ?array $data = [];

    public function mount(): void
    {
        $user = Auth::user();

        $this->form->fill([
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Profile Information')
                    ->description('Update your account profile information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Full Name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->required()
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Update Password')
                    ->description('Ensure your account is using a long, random password to stay secure')
                    ->schema([
                        Forms\Components\TextInput::make('current_password')
                            ->label('Current Password')
                            ->password()
                            ->revealable()
                            ->dehydrated(false)
                            ->required(fn ($get) => filled($get('new_password'))),
                        Forms\Components\TextInput::make('new_password')
                            ->label('New Password')
                            ->password()
                            ->revealable()
                            ->dehydrated(false)
                            ->confirmed()
                            ->minLength(8)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('new_password_confirmation')
                            ->label('Confirm New Password')
                            ->password()
                            ->revealable()
                            ->dehydrated(false),
                    ])
                    ->columns(3),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $user = Auth::user();

        // Update profile information
        $user->name = $data['name'];
        $user->email = $data['email'];

        // Update password if provided
        if (filled($data['new_password'])) {
            // Ensure current password is provided
            if (blank($data['current_password'])) {
                Notification::make()
                    ->title('Error')
                    ->body('The current password is required to set a new password.')
                    ->danger()
                    ->send();
                return;
            }

            // Verify current password
            if (!Hash::check($data['current_password'], $user->password)) {
                Notification::make()
                    ->title('Error')
                    ->body('The current password is incorrect.')
                    ->danger()
                    ->send();
                return;
            }

            // Update to new password
            // Note: Since User model has 'password' => 'hashed' cast, we assign the raw password
            $user->password = $data['new_password'];
        }

        $user->save();

        Notification::make()
            ->title('Profile updated successfully')
            ->success()
            ->send();

        // Update the form state with current data and clear password fields
        $this->form->fill([
            'name' => $user->name,
            'email' => $user->email,
            'current_password' => null,
            'new_password' => null,
            'new_password_confirmation' => null,
        ]);
    }
}
