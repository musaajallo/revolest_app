<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class SiteSettings extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'System Management';

    protected static ?string $navigationLabel = 'Site Settings';

    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.pages.site-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'site_name' => Setting::get('site_name', 'Revolest'),
            'site_tagline' => Setting::get('site_tagline', 'Your trusted real estate partner'),
            'site_logo' => Setting::get('site_logo'),
            'footer_logo' => Setting::get('footer_logo'),
            'site_favicon' => Setting::get('site_favicon'),
            'hero_background' => Setting::get('hero_background'),
            'banner_background' => Setting::get('banner_background'),
            'login_background' => Setting::get('login_background'),
            'contact_email' => Setting::get('contact_email', 'info@revolest.com'),
            'contact_phone' => Setting::get('contact_phone', '+220 123 4567'),
            'contact_phone_2' => Setting::get('contact_phone_2'),
            'contact_address' => Setting::get('contact_address', 'Kairaba Avenue, Serrekunda, The Gambia'),
            'business_hours' => Setting::get('business_hours', "Mon - Fri: 9:00 AM - 6:00 PM\nSat: 10:00 AM - 4:00 PM\nSun: Closed"),
            'facebook_url' => Setting::get('facebook_url'),
            'twitter_url' => Setting::get('twitter_url'),
            'instagram_url' => Setting::get('instagram_url'),
            'linkedin_url' => Setting::get('linkedin_url'),
            'youtube_url' => Setting::get('youtube_url'),
            'tiktok_url' => Setting::get('tiktok_url'),
            'footer_text' => Setting::get('footer_text', 'Your trusted partner in real estate. We help you find the perfect property for your needs.'),
            'policy_land_purchase' => Setting::get('policy.land_purchase'),
            'policy_land_sale' => Setting::get('policy.land_sale'),
            'policy_rental_consultation' => Setting::get('policy.rental_consultation'),
            'policy_rental_weekly_agent_fees' => Setting::get('policy.rental_weekly_agent_fees'),
            'policy_rental_yearly_agent_fees' => Setting::get('policy.rental_yearly_agent_fees'),
            'policy_built_property_listing' => Setting::get('policy.built_property_listing'),
            'policy_pet_application' => Setting::get('policy.pet_application'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Settings')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('General')
                            ->icon('heroicon-o-building-office')
                            ->schema([
                                Forms\Components\Section::make('Site Identity')
                                    ->description('Basic information about your website')
                                    ->schema([
                                        Forms\Components\TextInput::make('site_name')
                                            ->label('Site Name')
                                            ->required()
                                            ->maxLength(100),
                                        Forms\Components\TextInput::make('site_tagline')
                                            ->label('Tagline')
                                            ->maxLength(200),
                                        Forms\Components\FileUpload::make('site_logo')
                                            ->label('Site Logo')
                                            ->image()
                                            ->disk('public')
                                            ->directory('settings')
                                            ->visibility('public')
                                            ->imageResizeMode('contain')
                                            ->imageCropAspectRatio('3:1')
                                            ->imageResizeTargetWidth('300')
                                            ->helperText('Used in the public site header and admin panel. Recommended: 300×100 pixels, dark text on transparent background.'),
                                        Forms\Components\FileUpload::make('footer_logo')
                                            ->label('Footer Logo (white / inverse)')
                                            ->image()
                                            ->disk('public')
                                            ->directory('settings')
                                            ->visibility('public')
                                            ->imageResizeMode('contain')
                                            ->imageCropAspectRatio('3:1')
                                            ->imageResizeTargetWidth('300')
                                            ->helperText('Used in the dark public-site footer. Recommended: 300×100 pixels, white/light text on transparent background. Falls back to the Site Logo if not set.'),
                                        Forms\Components\FileUpload::make('site_favicon')
                                            ->label('Favicon')
                                            ->image()
                                            ->disk('public')
                                            ->directory('settings')
                                            ->visibility('public')
                                            ->imageResizeTargetWidth('32')
                                            ->imageResizeTargetHeight('32')
                                            ->helperText('Recommended size: 32x32 pixels'),
                                    ])->columns(2),
                            ]),

                        Forms\Components\Tabs\Tab::make('Backgrounds')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Forms\Components\Section::make('Public Site Backgrounds')
                                    ->description('Images shown on the public website. Each upload replaces the prior image. Leave blank to fall back to the default dark gradient.')
                                    ->schema([
                                        Forms\Components\FileUpload::make('hero_background')
                                            ->label('Home Hero Background')
                                            ->image()
                                            ->disk('public')
                                            ->directory('settings')
                                            ->visibility('public')
                                            ->imageResizeMode('cover')
                                            ->helperText('Shown behind the home page hero. Recommended: 1920×800, dark or muted (a 50% black overlay is applied automatically).'),
                                        Forms\Components\FileUpload::make('banner_background')
                                            ->label('Inner Page Banner Background')
                                            ->image()
                                            ->disk('public')
                                            ->directory('settings')
                                            ->visibility('public')
                                            ->imageResizeMode('cover')
                                            ->helperText('Shown behind the title banner on Properties, Agents, Contact, and Forms pages. Recommended: 1920×400.'),
                                        Forms\Components\FileUpload::make('login_background')
                                            ->label('Admin Login Background')
                                            ->image()
                                            ->disk('public')
                                            ->directory('settings')
                                            ->visibility('public')
                                            ->imageResizeMode('cover')
                                            ->helperText('Shown behind the /admin login form. Recommended: 1920×1080.'),
                                    ])->columns(1),
                            ]),

                        Forms\Components\Tabs\Tab::make('Contact')
                            ->icon('heroicon-o-phone')
                            ->schema([
                                Forms\Components\Section::make('Contact Information')
                                    ->description('Your business contact details')
                                    ->schema([
                                        Forms\Components\TextInput::make('contact_email')
                                            ->label('Email Address')
                                            ->email()
                                            ->required(),
                                        Forms\Components\TextInput::make('contact_phone')
                                            ->label('Primary Phone')
                                            ->tel(),
                                        Forms\Components\TextInput::make('contact_phone_2')
                                            ->label('Secondary Phone')
                                            ->tel(),
                                        Forms\Components\Textarea::make('contact_address')
                                            ->label('Office Address')
                                            ->rows(3),
                                        Forms\Components\Textarea::make('business_hours')
                                            ->label('Business Hours')
                                            ->rows(4)
                                            ->helperText('Enter each day on a new line'),
                                    ])->columns(2),
                            ]),

                        Forms\Components\Tabs\Tab::make('Social Media')
                            ->icon('heroicon-o-share')
                            ->schema([
                                Forms\Components\Section::make('Social Media Links')
                                    ->description('Connect your social media accounts')
                                    ->schema([
                                        Forms\Components\TextInput::make('facebook_url')
                                            ->label('Facebook')
                                            ->url()
                                            ->prefix('https://')
                                            ->placeholder('facebook.com/yourpage'),
                                        Forms\Components\TextInput::make('twitter_url')
                                            ->label('Twitter / X')
                                            ->url()
                                            ->prefix('https://')
                                            ->placeholder('twitter.com/yourhandle'),
                                        Forms\Components\TextInput::make('instagram_url')
                                            ->label('Instagram')
                                            ->url()
                                            ->prefix('https://')
                                            ->placeholder('instagram.com/yourhandle'),
                                        Forms\Components\TextInput::make('linkedin_url')
                                            ->label('LinkedIn')
                                            ->url()
                                            ->prefix('https://')
                                            ->placeholder('linkedin.com/company/yourcompany'),
                                        Forms\Components\TextInput::make('youtube_url')
                                            ->label('YouTube')
                                            ->url()
                                            ->prefix('https://')
                                            ->placeholder('youtube.com/@yourchannel'),
                                        Forms\Components\TextInput::make('tiktok_url')
                                            ->label('TikTok')
                                            ->url()
                                            ->prefix('https://')
                                            ->placeholder('tiktok.com/@yourhandle'),
                                    ])->columns(2),
                            ]),

                        Forms\Components\Tabs\Tab::make('Footer')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Forms\Components\Section::make('Footer Content')
                                    ->description('Customize your website footer')
                                    ->schema([
                                        Forms\Components\Textarea::make('footer_text')
                                            ->label('Footer Description')
                                            ->rows(3)
                                            ->columnSpanFull()
                                            ->helperText('The text that appears below the logo in the footer'),
                                        Forms\Components\Placeholder::make('copyright_info')
                                            ->label('Copyright Notice')
                                            ->content('© '.date('Y').' [Site Name]. All rights reserved.')
                                            ->helperText('The copyright year updates automatically based on the current year.'),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Form Policies')
                            ->icon('heroicon-o-document-check')
                            ->schema([
                                Forms\Components\Section::make('Public Form Policies & Fees')
                                    ->description('Markdown content shown above the signature block on each public form. Edits take effect immediately — no redeploy needed.')
                                    ->schema([
                                        Forms\Components\Textarea::make('policy_land_purchase')
                                            ->label('Land Purchase')
                                            ->rows(10)
                                            ->columnSpanFull(),
                                        Forms\Components\Textarea::make('policy_land_sale')
                                            ->label('Land Sale / Listing')
                                            ->rows(8)
                                            ->columnSpanFull(),
                                        Forms\Components\Textarea::make('policy_rental_consultation')
                                            ->label('Rental Consultation')
                                            ->rows(14)
                                            ->columnSpanFull(),
                                        Forms\Components\Textarea::make('policy_rental_weekly_agent_fees')
                                            ->label('Rental — Weekly Agent Fees')
                                            ->rows(10)
                                            ->columnSpanFull(),
                                        Forms\Components\Textarea::make('policy_rental_yearly_agent_fees')
                                            ->label('Rental — Yearly Agent Fees')
                                            ->rows(12)
                                            ->columnSpanFull(),
                                        Forms\Components\Textarea::make('policy_built_property_listing')
                                            ->label('Built Property Listing')
                                            ->rows(8)
                                            ->columnSpanFull(),
                                        Forms\Components\Textarea::make('policy_pet_application')
                                            ->label('Pet Application')
                                            ->rows(12)
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $settings = [
            ['key' => 'site_name', 'data_key' => 'site_name', 'label' => 'Site Name', 'group' => 'site', 'type' => 'text'],
            ['key' => 'site_tagline', 'data_key' => 'site_tagline', 'label' => 'Site Tagline', 'group' => 'site', 'type' => 'text'],
            ['key' => 'site_logo', 'data_key' => 'site_logo', 'label' => 'Site Logo', 'group' => 'site', 'type' => 'image'],
            ['key' => 'footer_logo', 'data_key' => 'footer_logo', 'label' => 'Footer Logo (white)', 'group' => 'site', 'type' => 'image'],
            ['key' => 'site_favicon', 'data_key' => 'site_favicon', 'label' => 'Site Favicon', 'group' => 'site', 'type' => 'image'],
            ['key' => 'hero_background', 'data_key' => 'hero_background', 'label' => 'Home Hero Background', 'group' => 'site', 'type' => 'image'],
            ['key' => 'banner_background', 'data_key' => 'banner_background', 'label' => 'Inner Page Banner Background', 'group' => 'site', 'type' => 'image'],
            ['key' => 'login_background', 'data_key' => 'login_background', 'label' => 'Admin Login Background', 'group' => 'site', 'type' => 'image'],
            ['key' => 'contact_email', 'data_key' => 'contact_email', 'label' => 'Contact Email', 'group' => 'contact', 'type' => 'text'],
            ['key' => 'contact_phone', 'data_key' => 'contact_phone', 'label' => 'Contact Phone', 'group' => 'contact', 'type' => 'text'],
            ['key' => 'contact_phone_2', 'data_key' => 'contact_phone_2', 'label' => 'Contact Phone 2', 'group' => 'contact', 'type' => 'text'],
            ['key' => 'contact_address', 'data_key' => 'contact_address', 'label' => 'Contact Address', 'group' => 'contact', 'type' => 'textarea'],
            ['key' => 'business_hours', 'data_key' => 'business_hours', 'label' => 'Business Hours', 'group' => 'contact', 'type' => 'textarea'],
            ['key' => 'facebook_url', 'data_key' => 'facebook_url', 'label' => 'Facebook URL', 'group' => 'social', 'type' => 'text'],
            ['key' => 'twitter_url', 'data_key' => 'twitter_url', 'label' => 'Twitter URL', 'group' => 'social', 'type' => 'text'],
            ['key' => 'instagram_url', 'data_key' => 'instagram_url', 'label' => 'Instagram URL', 'group' => 'social', 'type' => 'text'],
            ['key' => 'linkedin_url', 'data_key' => 'linkedin_url', 'label' => 'LinkedIn URL', 'group' => 'social', 'type' => 'text'],
            ['key' => 'youtube_url', 'data_key' => 'youtube_url', 'label' => 'YouTube URL', 'group' => 'social', 'type' => 'text'],
            ['key' => 'tiktok_url', 'data_key' => 'tiktok_url', 'label' => 'TikTok URL', 'group' => 'social', 'type' => 'text'],
            ['key' => 'footer_text', 'data_key' => 'footer_text', 'label' => 'Footer Text', 'group' => 'site', 'type' => 'textarea'],
            ['key' => 'policy.land_purchase', 'data_key' => 'policy_land_purchase', 'label' => 'Land Purchase Policy', 'group' => 'policies', 'type' => 'textarea'],
            ['key' => 'policy.land_sale', 'data_key' => 'policy_land_sale', 'label' => 'Land Sale / Listing Policy', 'group' => 'policies', 'type' => 'textarea'],
            ['key' => 'policy.rental_consultation', 'data_key' => 'policy_rental_consultation', 'label' => 'Rental Consultation Policy', 'group' => 'policies', 'type' => 'textarea'],
            ['key' => 'policy.rental_weekly_agent_fees', 'data_key' => 'policy_rental_weekly_agent_fees', 'label' => 'Rental — Weekly Agent Fees', 'group' => 'policies', 'type' => 'textarea'],
            ['key' => 'policy.rental_yearly_agent_fees', 'data_key' => 'policy_rental_yearly_agent_fees', 'label' => 'Rental — Yearly Agent Fees', 'group' => 'policies', 'type' => 'textarea'],
            ['key' => 'policy.built_property_listing', 'data_key' => 'policy_built_property_listing', 'label' => 'Built Property Listing Policy', 'group' => 'policies', 'type' => 'textarea'],
            ['key' => 'policy.pet_application', 'data_key' => 'policy_pet_application', 'label' => 'Pet Application Agreement', 'group' => 'policies', 'type' => 'textarea'],
        ];

        foreach ($settings as $index => $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                [
                    'group' => $setting['group'],
                    'label' => $setting['label'],
                    'type' => $setting['type'],
                    'value' => $data[$setting['data_key']] ?? null,
                    'order' => $index,
                ]
            );
        }

        Setting::clearCache();

        Notification::make()
            ->title('Settings saved successfully')
            ->success()
            ->send();
    }

    public static function canAccess(): bool
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        if (! $user) {
            return false;
        }

        return in_array($user->role, ['super_admin', 'admin']);
    }
}
