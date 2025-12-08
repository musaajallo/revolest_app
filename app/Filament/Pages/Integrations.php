<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class Integrations extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-puzzle-piece';
    protected static ?string $navigationGroup = 'System Management';
    protected static ?string $navigationLabel = 'Integrations';
    protected static ?int $navigationSort = 3;
    protected static string $view = 'filament.pages.integrations';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            // Google Analytics
            'google_analytics_enabled' => Setting::get('google_analytics_enabled', false),
            'google_analytics_id' => Setting::get('google_analytics_id'),
            'google_analytics_4_id' => Setting::get('google_analytics_4_id'),

            // Matomo Analytics
            'matomo_enabled' => Setting::get('matomo_enabled', false),
            'matomo_url' => Setting::get('matomo_url'),
            'matomo_site_id' => Setting::get('matomo_site_id'),

            // Google Maps
            'google_maps_enabled' => Setting::get('google_maps_enabled', false),
            'google_maps_api_key' => Setting::get('google_maps_api_key'),
            'google_maps_default_lat' => Setting::get('google_maps_default_lat', '13.4549'),
            'google_maps_default_lng' => Setting::get('google_maps_default_lng', '-16.5790'),
            'google_maps_default_zoom' => Setting::get('google_maps_default_zoom', '12'),

            // WhatsApp Business
            'whatsapp_enabled' => Setting::get('whatsapp_enabled', false),
            'whatsapp_phone_number' => Setting::get('whatsapp_phone_number'),
            'whatsapp_business_id' => Setting::get('whatsapp_business_id'),
            'whatsapp_access_token' => Setting::get('whatsapp_access_token'),
            'whatsapp_default_message' => Setting::get('whatsapp_default_message', 'Hello! I\'m interested in your properties.'),
            'whatsapp_show_floating_button' => Setting::get('whatsapp_show_floating_button', true),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Google Analytics')
                    ->description('Track website traffic and user behavior with Google Analytics')
                    ->icon('heroicon-o-chart-bar')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Toggle::make('google_analytics_enabled')
                            ->label('Enable Google Analytics')
                            ->live()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('google_analytics_id')
                            ->label('Universal Analytics ID (UA)')
                            ->placeholder('UA-XXXXXXXXX-X')
                            ->helperText('Legacy Universal Analytics tracking ID')
                            ->visible(fn (Forms\Get $get) => $get('google_analytics_enabled')),
                        Forms\Components\TextInput::make('google_analytics_4_id')
                            ->label('Google Analytics 4 ID (GA4)')
                            ->placeholder('G-XXXXXXXXXX')
                            ->helperText('New Google Analytics 4 measurement ID')
                            ->visible(fn (Forms\Get $get) => $get('google_analytics_enabled')),
                        Forms\Components\Placeholder::make('ga_info')
                            ->content('To get your Google Analytics ID, visit Google Analytics and create a property for your website.')
                            ->visible(fn (Forms\Get $get) => $get('google_analytics_enabled'))
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Matomo Analytics')
                    ->description('Open-source, privacy-focused web analytics platform')
                    ->icon('heroicon-o-presentation-chart-line')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Forms\Components\Toggle::make('matomo_enabled')
                            ->label('Enable Matomo Analytics')
                            ->live()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('matomo_url')
                            ->label('Matomo URL')
                            ->url()
                            ->placeholder('https://your-matomo-instance.com')
                            ->helperText('The URL of your Matomo installation')
                            ->visible(fn (Forms\Get $get) => $get('matomo_enabled')),
                        Forms\Components\TextInput::make('matomo_site_id')
                            ->label('Site ID')
                            ->numeric()
                            ->placeholder('1')
                            ->helperText('Your website\'s ID in Matomo')
                            ->visible(fn (Forms\Get $get) => $get('matomo_enabled')),
                        Forms\Components\Placeholder::make('matomo_info')
                            ->content('Matomo is a great privacy-respecting alternative to Google Analytics. You can self-host it or use Matomo Cloud.')
                            ->visible(fn (Forms\Get $get) => $get('matomo_enabled'))
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Google Maps')
                    ->description('Display interactive maps for property locations')
                    ->icon('heroicon-o-map')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Forms\Components\Toggle::make('google_maps_enabled')
                            ->label('Enable Google Maps')
                            ->live()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('google_maps_api_key')
                            ->label('API Key')
                            ->password()
                            ->revealable()
                            ->placeholder('AIzaSy...')
                            ->helperText('Your Google Maps JavaScript API key')
                            ->visible(fn (Forms\Get $get) => $get('google_maps_enabled'))
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('google_maps_default_lat')
                            ->label('Default Latitude')
                            ->numeric()
                            ->placeholder('13.4549')
                            ->visible(fn (Forms\Get $get) => $get('google_maps_enabled')),
                        Forms\Components\TextInput::make('google_maps_default_lng')
                            ->label('Default Longitude')
                            ->numeric()
                            ->placeholder('-16.5790')
                            ->visible(fn (Forms\Get $get) => $get('google_maps_enabled')),
                        Forms\Components\Select::make('google_maps_default_zoom')
                            ->label('Default Zoom Level')
                            ->options([
                                '8' => '8 - Region',
                                '10' => '10 - City',
                                '12' => '12 - Neighborhood',
                                '14' => '14 - Streets',
                                '16' => '16 - Buildings',
                            ])
                            ->visible(fn (Forms\Get $get) => $get('google_maps_enabled')),
                        Forms\Components\Placeholder::make('maps_info')
                            ->content('To get a Google Maps API key, visit the Google Cloud Console and enable the Maps JavaScript API.')
                            ->visible(fn (Forms\Get $get) => $get('google_maps_enabled'))
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('WhatsApp Business')
                    ->description('Connect with customers via WhatsApp Business API')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Forms\Components\Toggle::make('whatsapp_enabled')
                            ->label('Enable WhatsApp Integration')
                            ->live()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('whatsapp_phone_number')
                            ->label('WhatsApp Phone Number')
                            ->placeholder('+220 123 4567')
                            ->tel()
                            ->helperText('Your WhatsApp Business phone number with country code')
                            ->visible(fn (Forms\Get $get) => $get('whatsapp_enabled')),
                        Forms\Components\TextInput::make('whatsapp_business_id')
                            ->label('Business Account ID')
                            ->placeholder('XXXXXXXXXXXXXXXXX')
                            ->helperText('Your WhatsApp Business Account ID (optional for API)')
                            ->visible(fn (Forms\Get $get) => $get('whatsapp_enabled')),
                        Forms\Components\TextInput::make('whatsapp_access_token')
                            ->label('Access Token')
                            ->password()
                            ->revealable()
                            ->placeholder('EAAG...')
                            ->helperText('Your WhatsApp Cloud API access token (optional for API)')
                            ->visible(fn (Forms\Get $get) => $get('whatsapp_enabled'))
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('whatsapp_default_message')
                            ->label('Default Message')
                            ->placeholder('Hello! I\'m interested in your properties.')
                            ->rows(3)
                            ->helperText('Pre-filled message when users click the WhatsApp button')
                            ->visible(fn (Forms\Get $get) => $get('whatsapp_enabled'))
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('whatsapp_show_floating_button')
                            ->label('Show Floating WhatsApp Button')
                            ->helperText('Display a floating WhatsApp button on all public pages')
                            ->visible(fn (Forms\Get $get) => $get('whatsapp_enabled'))
                            ->columnSpanFull(),
                        Forms\Components\Placeholder::make('whatsapp_info')
                            ->content('For basic click-to-chat, only the phone number is required. For API features (automated messages, notifications), you need a WhatsApp Business Account and access token from Meta Business Suite.')
                            ->visible(fn (Forms\Get $get) => $get('whatsapp_enabled'))
                            ->columnSpanFull(),
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $settings = [
            // Google Analytics
            ['key' => 'google_analytics_enabled', 'label' => 'Google Analytics Enabled', 'group' => 'integration', 'type' => 'boolean'],
            ['key' => 'google_analytics_id', 'label' => 'Google Analytics ID (UA)', 'group' => 'integration', 'type' => 'text'],
            ['key' => 'google_analytics_4_id', 'label' => 'Google Analytics 4 ID', 'group' => 'integration', 'type' => 'text'],

            // Matomo
            ['key' => 'matomo_enabled', 'label' => 'Matomo Enabled', 'group' => 'integration', 'type' => 'boolean'],
            ['key' => 'matomo_url', 'label' => 'Matomo URL', 'group' => 'integration', 'type' => 'text'],
            ['key' => 'matomo_site_id', 'label' => 'Matomo Site ID', 'group' => 'integration', 'type' => 'text'],

            // Google Maps
            ['key' => 'google_maps_enabled', 'label' => 'Google Maps Enabled', 'group' => 'integration', 'type' => 'boolean'],
            ['key' => 'google_maps_api_key', 'label' => 'Google Maps API Key', 'group' => 'integration', 'type' => 'text'],
            ['key' => 'google_maps_default_lat', 'label' => 'Default Latitude', 'group' => 'integration', 'type' => 'text'],
            ['key' => 'google_maps_default_lng', 'label' => 'Default Longitude', 'group' => 'integration', 'type' => 'text'],
            ['key' => 'google_maps_default_zoom', 'label' => 'Default Zoom', 'group' => 'integration', 'type' => 'text'],

            // WhatsApp Business
            ['key' => 'whatsapp_enabled', 'label' => 'WhatsApp Enabled', 'group' => 'integration', 'type' => 'boolean'],
            ['key' => 'whatsapp_phone_number', 'label' => 'WhatsApp Phone Number', 'group' => 'integration', 'type' => 'text'],
            ['key' => 'whatsapp_business_id', 'label' => 'WhatsApp Business ID', 'group' => 'integration', 'type' => 'text'],
            ['key' => 'whatsapp_access_token', 'label' => 'WhatsApp Access Token', 'group' => 'integration', 'type' => 'text'],
            ['key' => 'whatsapp_default_message', 'label' => 'WhatsApp Default Message', 'group' => 'integration', 'type' => 'text'],
            ['key' => 'whatsapp_show_floating_button', 'label' => 'Show WhatsApp Floating Button', 'group' => 'integration', 'type' => 'boolean'],
        ];

        foreach ($settings as $index => $setting) {
            $value = $data[$setting['key']] ?? null;

            // Convert boolean to string for storage
            if ($setting['type'] === 'boolean') {
                $value = $value ? '1' : '0';
            }

            Setting::updateOrCreate(
                ['key' => $setting['key']],
                [
                    'group' => $setting['group'],
                    'label' => $setting['label'],
                    'type' => $setting['type'],
                    'value' => $value,
                    'order' => $index,
                ]
            );
        }

        Setting::clearCache();

        Notification::make()
            ->title('Integrations saved successfully')
            ->success()
            ->send();
    }
}
