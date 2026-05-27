<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseBuildPropertyLeadResource\Pages;
use App\Models\PurchaseBuildPropertyLead;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PurchaseBuildPropertyLeadResource extends Resource
{
    protected static ?string $model = PurchaseBuildPropertyLead::class;

    protected static ?string $navigationGroup = 'Submissions';

    protected static ?string $navigationLabel = 'Build Property Purchase Leads';

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';

    protected static ?int $navigationSort = 13;

    public static function getGlobalSearchResultUrl($record): string
    {
        return static::getUrl('view', ['record' => $record->getKey()]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['full_name', 'email', 'phone_primary'];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Contact')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('full_name')->required()->maxLength(255),
                    Forms\Components\TextInput::make('email')->email()->maxLength(255),
                    Forms\Components\TextInput::make('phone_primary')->required()->tel()->maxLength(50),
                    Forms\Components\TextInput::make('phone_secondary')->tel()->maxLength(50),
                    Forms\Components\TextInput::make('phone_tertiary')->tel()->maxLength(50),
                    Forms\Components\TextInput::make('current_address')->maxLength(255),
                ]),
            Forms\Components\Section::make('Preferences')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('property_type')->options([
                        'house' => 'House',
                        'mansion' => 'Mansion',
                        'apartment' => 'Apartment',
                    ]),
                    Forms\Components\TextInput::make('build_status')->maxLength(100),
                    Forms\Components\TextInput::make('preferred_location')->maxLength(255)->columnSpanFull(),
                    Forms\Components\Textarea::make('avoid_areas')->columnSpanFull(),
                    Forms\Components\TextInput::make('architectural_style')->maxLength(255),
                    Forms\Components\TextInput::make('bedrooms_bathrooms')->maxLength(255),
                    Forms\Components\Textarea::make('special_features')->columnSpanFull(),
                    Forms\Components\Textarea::make('luxury_features')->columnSpanFull(),
                ]),
            Forms\Components\Section::make('Budget & Financing')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('budget')->maxLength(255),
                    Forms\Components\Select::make('financing_method')->options([
                        'mortgage' => 'Mortgage',
                        'cash' => 'Cash',
                        'mixed' => 'Mixed',
                        'other' => 'Other',
                    ]),
                    Forms\Components\TextInput::make('mortgage_preapproval')->maxLength(255),
                    Forms\Components\Toggle::make('needs_mortgage_advice'),
                    Forms\Components\Toggle::make('open_to_negotiation'),
                ]),
            Forms\Components\Section::make('Size & Layout')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('min_square_footage')->maxLength(100),
                    Forms\Components\Toggle::make('needs_extra_space'),
                    Forms\Components\TextInput::make('lot_size_preference')->maxLength(255),
                    Forms\Components\Select::make('storey_preference')->options([
                        'single' => 'Single-story',
                        'multi' => 'Multi-story',
                        'no_preference' => 'No preference',
                    ]),
                    Forms\Components\Textarea::make('layout_preference')->columnSpanFull(),
                ]),
            Forms\Components\Section::make('Location & Neighborhood')
                ->columns(2)
                ->schema([
                    Forms\Components\Textarea::make('proximity_preference')->columnSpanFull(),
                    Forms\Components\Select::make('area_kind')->options([
                        'city' => 'City',
                        'suburban' => 'Suburban',
                        'rural' => 'Rural',
                    ]),
                    Forms\Components\Textarea::make('amenities_importance')->columnSpanFull(),
                    Forms\Components\Select::make('community_type')->options([
                        'gated' => 'Gated',
                        'private' => 'Private',
                        'open' => 'Open',
                    ]),
                    Forms\Components\Textarea::make('landmarks')->columnSpanFull(),
                ]),
            Forms\Components\Section::make('Timeline & Goals')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('move_in_target')->maxLength(100),
                    Forms\Components\Textarea::make('time_sensitivity')->columnSpanFull(),
                    Forms\Components\Select::make('readiness_preference')->options([
                        'ready' => 'Move-in ready',
                        'under_construction' => 'Under construction OK',
                        'renovation_ok' => 'Renovation OK',
                    ]),
                    Forms\Components\Select::make('use_purpose')->options([
                        'primary' => 'Primary residence',
                        'vacation' => 'Vacation home',
                        'investment' => 'Investment',
                    ]),
                    Forms\Components\Textarea::make('long_term_value')->columnSpanFull(),
                    Forms\Components\Toggle::make('open_to_developments'),
                ]),
            Forms\Components\Section::make('Legal, Maintenance & Misc')
                ->columns(2)
                ->schema([
                    Forms\Components\Textarea::make('legal_requirements')->columnSpanFull(),
                    Forms\Components\Toggle::make('needs_inspection_help'),
                    Forms\Components\TextInput::make('maintenance_effort')->maxLength(255),
                    Forms\Components\Select::make('maintenance_preference')->options([
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                    ]),
                    Forms\Components\Textarea::make('additional_services')->columnSpanFull(),
                    Forms\Components\Select::make('household_type')->options([
                        'alone' => 'Alone',
                        'family' => 'Family',
                        'with_pets' => 'With pets',
                    ]),
                    Forms\Components\Textarea::make('accessibility_needs')->columnSpanFull(),
                    Forms\Components\Textarea::make('pet_accommodations')->columnSpanFull(),
                    Forms\Components\Select::make('eco_priority')->options([
                        'high' => 'High',
                        'medium' => 'Medium',
                        'low' => 'Low',
                        'none' => 'None',
                    ]),
                    Forms\Components\Toggle::make('smart_home_interest'),
                    Forms\Components\Toggle::make('customizable_required'),
                    Forms\Components\Toggle::make('needs_reno_design_help'),
                    Forms\Components\Textarea::make('resale_plan')->columnSpanFull(),
                    Forms\Components\Select::make('property_age_preference')->options([
                        'new' => 'Brand-new',
                        'older' => 'Older',
                        'no_preference' => 'No preference',
                    ]),
                    Forms\Components\Select::make('turnkey_preference')->options([
                        'turnkey' => 'Turnkey',
                        'personalize' => 'Personalize',
                        'either' => 'Either',
                    ]),
                    Forms\Components\Textarea::make('other_considerations')->columnSpanFull(),
                ]),
            Forms\Components\Section::make('Referral & Triage')
                ->columns(2)
                ->schema([
                    Forms\Components\Toggle::make('previous_company_contact')->label('Contacted other companies before'),
                    Forms\Components\Textarea::make('previous_company_experience')->columnSpanFull(),
                    Forms\Components\TextInput::make('referral_source')->maxLength(255),
                    Forms\Components\TextInput::make('referral_name')->maxLength(255),
                    Forms\Components\Textarea::make('notes')->label('Internal notes')->columnSpanFull(),
                    Forms\Components\Select::make('status')
                        ->options(array_combine(PurchaseBuildPropertyLead::STATUSES, PurchaseBuildPropertyLead::STATUSES))
                        ->required(),
                    Forms\Components\Select::make('agent_id')
                        ->label('Assigned Agent')
                        ->relationship('agent', 'name')
                        ->searchable(),
                ]),
            Forms\Components\Section::make('Standardized Fields')
                ->description('Common fields shared across all lead types — match the Excel oversight columns.')
                ->columns(2)
                ->collapsible()
                ->schema([
                    Forms\Components\TextInput::make('budget_min')->numeric()->prefix('D')->label('Budget Min'),
                    Forms\Components\TextInput::make('budget_max')->numeric()->prefix('D')->label('Budget Max'),
                    Forms\Components\TextInput::make('bedrooms')->numeric()->minValue(0)->maxValue(20),
                    Forms\Components\TextInput::make('bathrooms')->numeric()->minValue(0)->maxValue(20),
                    Forms\Components\TextInput::make('plot_size')->maxLength(100),
                    Forms\Components\Select::make('property_condition')->options([
                        'new' => 'New',
                        'existing' => 'Existing',
                        'needs_renovation' => 'Needs renovation',
                    ]),
                    Forms\Components\Select::make('intended_use')->options([
                        'residential' => 'Residential',
                        'commercial' => 'Commercial',
                        'investment' => 'Investment',
                        'mixed' => 'Mixed',
                    ]),
                    Forms\Components\TextInput::make('referred_by_name')
                        ->label('Referred by')
                        ->helperText('Staff member / source who brought this lead in.'),
                ]),
            Forms\Components\Section::make('Acknowledgement')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('signed_name')->maxLength(255),
                    Forms\Components\DateTimePicker::make('signed_at'),
                    Forms\Components\TextInput::make('ip_address')->disabled(),
                    Forms\Components\DateTimePicker::make('submitted_at')->disabled(),
                ])->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('full_name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('phone_primary')->label('Phone')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('property_type')->badge()->toggleable(),
                Tables\Columns\TextColumn::make('budget')->toggleable(),
                Tables\Columns\TextColumn::make('preferred_location')->limit(40)->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'warning',
                        'in_review' => 'info',
                        'contacted' => 'primary',
                        'converted' => 'success',
                        'closed' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('agent.name')->label('Agent')->toggleable(),
                Tables\Columns\TextColumn::make('submitted_at')->dateTime()->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(array_combine(PurchaseBuildPropertyLead::STATUSES, PurchaseBuildPropertyLead::STATUSES)),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->color('success'),
                Tables\Actions\EditAction::make()->color('warning'),
                Tables\Actions\DeleteAction::make()->color('danger'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPurchaseBuildPropertyLeads::route('/'),
            'create' => Pages\CreatePurchaseBuildPropertyLead::route('/create'),
            'view' => Pages\ViewPurchaseBuildPropertyLead::route('/{record}'),
            'edit' => Pages\EditPurchaseBuildPropertyLead::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('status', 'new')->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function canAccess(): bool
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        if (! $user) {
            return false;
        }

        return in_array($user->role, ['super_admin', 'admin', 'agent']);
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\RelationManagers\LeadActivitiesRelationManager::class,
        ];
    }
}
