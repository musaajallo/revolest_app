<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BuiltPropertyListingLeadResource\Pages;
use App\Models\BuiltPropertyListingLead;
use App\Models\Property;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BuiltPropertyListingLeadResource extends Resource
{
    protected static ?string $model = BuiltPropertyListingLead::class;

    protected static ?string $navigationGroup = 'Submissions';

    protected static ?string $navigationLabel = 'Property Listing Leads';

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?int $navigationSort = 40;

    public static function getGlobalSearchResultUrl($record): string
    {
        return static::getUrl('view', ['record' => $record->getKey()]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['first_name', 'last_name', 'email', 'phone', 'property_address'];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Owner Contact')->columns(2)->schema([
                Forms\Components\TextInput::make('first_name')->required()->maxLength(100),
                Forms\Components\TextInput::make('last_name')->required()->maxLength(100),
                Forms\Components\TextInput::make('nationality')->maxLength(100),
                Forms\Components\TextInput::make('email')->email()->maxLength(255),
                Forms\Components\TextInput::make('phone')->required()->tel()->maxLength(50),
                Forms\Components\TextInput::make('street_address')->maxLength(255),
                Forms\Components\TextInput::make('city')->label('City / Town / Province')->maxLength(100),
                Forms\Components\TextInput::make('region')->maxLength(100),
            ]),
            Forms\Components\Section::make('Property Description')->columns(2)->schema([
                Forms\Components\Textarea::make('legal_description')->columnSpanFull(),
                Forms\Components\TextInput::make('property_address')->required()->maxLength(255),
                Forms\Components\TextInput::make('land_dimension')->maxLength(100),
                Forms\Components\TextInput::make('approximate_sqft')->maxLength(50),
                Forms\Components\Select::make('property_status')->options([
                    'freehold' => 'Freehold',
                    'leasehold' => 'Leasehold',
                ]),
                Forms\Components\Select::make('property_type')->options([
                    'residential' => 'Residential',
                    'single_family' => 'Single-family',
                    'multifamily' => 'Multifamily',
                    'commercial' => 'Commercial',
                    'recreation' => 'Recreation',
                    'farm' => 'Farm',
                    'industrial' => 'Industrial',
                    'land' => 'Land',
                    'other' => 'Other',
                ]),
                Forms\Components\CheckboxList::make('buildings_on_property')->options([
                    'house' => 'House',
                    'workshop' => 'Workshop',
                    'garage_single' => 'Garage (single)',
                    'garage_double' => 'Garage (double)',
                    'store' => 'Store',
                    'as_is' => 'As-is',
                    'other' => 'Other',
                ])->columns(2)->columnSpanFull(),
                Forms\Components\TextInput::make('asking_price')->numeric()->prefix('D'),
                Forms\Components\Select::make('possession')->options([
                    'immediately' => 'Immediately',
                    'leased' => 'Leased',
                    'at_closing' => 'At Closing',
                    'to_be_arranged' => 'To be arranged',
                    'other' => 'Other',
                ]),
            ]),
            Forms\Components\Section::make('Showing & Building Detail')->columns(2)->schema([
                Forms\Components\Select::make('showing_instructions')->options([
                    'call_office' => 'Call listing office',
                    'show_anytime' => 'Show anytime',
                    'vacant' => 'Vacant',
                    'appointment_only' => 'Appointment only',
                    'other' => 'Other',
                ]),
                Forms\Components\TextInput::make('number_of_rooms')->numeric()->minValue(0),
                Forms\Components\TextInput::make('bedrooms_detail')->maxLength(255),
                Forms\Components\TextInput::make('bathrooms_detail')->maxLength(255),
                Forms\Components\TextInput::make('age_of_house')->maxLength(100),
                Forms\Components\TextInput::make('square_footage')->maxLength(100),
                Forms\Components\TextInput::make('roof_type')->maxLength(100),
                Forms\Components\TextInput::make('furnace')->maxLength(100),
                Forms\Components\Textarea::make('amenities')->columnSpanFull(),
                Forms\Components\CheckboxList::make('natural_features')->options([
                    'hilly' => 'Hilly / Steep',
                    'open' => 'Open',
                    'slope' => 'Slope',
                    'rolling' => 'Rolling',
                    'flat' => 'Flat',
                    'other' => 'Other',
                ])->columns(2)->columnSpanFull(),
                Forms\Components\CheckboxList::make('site_documents')->options([
                    'site_plan' => 'Site Plan',
                    'topo_plan' => 'Topo Plan',
                    'aerial_photo' => 'Aerial Photo',
                ])->columns(3)->columnSpanFull(),
            ]),
            Forms\Components\Section::make('Disclosures & Documents')->columns(2)->schema([
                Forms\Components\CheckboxList::make('disclosures')->options([
                    'flood' => 'Flood-affected area',
                    'appliance' => 'Appliance issue',
                    'security' => 'Security concerns',
                    'water' => 'Water issues',
                    'pest' => 'Pest infestations',
                ])->columns(2)->columnSpanFull(),
                Forms\Components\Textarea::make('disclosures_other')->columnSpanFull(),
                Forms\Components\CheckboxList::make('documents_attached')->options([
                    'title_deed' => 'Title deed',
                    'id_passport' => 'ID / Passport',
                    'physical_planning' => 'Physical planning documents',
                    'tax_rate' => 'Tax rate documents',
                    'lease_assignment' => 'Lease Assignment',
                    'building_plan' => 'Building plan',
                    'recent_appraisal' => 'Recent appraisal report',
                    'survey' => 'Survey of the property',
                ])->columns(2)->columnSpanFull(),
            ]),
            Forms\Components\Section::make('Referral')->columns(2)->schema([
                Forms\Components\Toggle::make('previous_company_contact'),
                Forms\Components\Textarea::make('previous_company_experience')->columnSpanFull(),
                Forms\Components\TextInput::make('referral_source')->maxLength(255),
                Forms\Components\TextInput::make('referral_name')->maxLength(255),
            ])->collapsed(),
            Forms\Components\Section::make('Triage')->columns(2)->schema([
                Forms\Components\Select::make('status')
                    ->options(array_combine(BuiltPropertyListingLead::STATUSES, BuiltPropertyListingLead::STATUSES))
                    ->required(),
                Forms\Components\Select::make('agent_id')->relationship('agent', 'name')->searchable(),
                Forms\Components\Select::make('converted_property_id')
                    ->label('Converted Property')
                    ->relationship('convertedProperty', 'title')
                    ->searchable(),
                Forms\Components\Textarea::make('notes')->label('Internal notes')->columnSpanFull(),
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
                    Forms\Components\Toggle::make('furnished')->inline(false),
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
            Forms\Components\Section::make('Acknowledgement')->columns(2)->schema([
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
                Tables\Columns\TextColumn::make('full_name')
                    ->getStateUsing(fn (BuiltPropertyListingLead $r) => $r->full_name)
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    })
                    ->sortable(['first_name', 'last_name']),
                Tables\Columns\TextColumn::make('phone')->searchable(),
                Tables\Columns\TextColumn::make('property_address')->searchable()->limit(40),
                Tables\Columns\TextColumn::make('property_type')->badge()->toggleable(),
                Tables\Columns\TextColumn::make('asking_price')->money('GMD')->toggleable(),
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
                Tables\Columns\TextColumn::make('convertedProperty.title')->label('Converted →')->toggleable(),
                Tables\Columns\TextColumn::make('submitted_at')->dateTime()->sortable()->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(array_combine(BuiltPropertyListingLead::STATUSES, BuiltPropertyListingLead::STATUSES)),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->color('success'),
                Tables\Actions\EditAction::make()->color('warning'),
                Tables\Actions\Action::make('convert')
                    ->label('Convert to Property')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (BuiltPropertyListingLead $record) => $record->converted_property_id === null)
                    ->action(function (BuiltPropertyListingLead $record): void {
                        $type = match ($record->property_type) {
                            'residential', 'single_family' => 'house',
                            'multifamily' => 'apartment',
                            'commercial' => 'commercial',
                            'land' => 'land',
                            default => 'house',
                        };

                        $property = Property::create([
                            'title' => trim(($record->property_type ? ucfirst(str_replace('_', ' ', $record->property_type)).' at ' : 'Property at ').$record->property_address),
                            'description' => $record->amenities,
                            'address' => $record->property_address,
                            'price' => $record->asking_price ?? 0,
                            'sale_price' => $record->asking_price,
                            'type' => $type,
                            'purpose' => 'sale',
                            'listing_category' => $type === 'land' ? 'land' : 'building',
                            'status' => 'inactive',
                        ]);

                        $record->update([
                            'converted_property_id' => $property->id,
                            'status' => 'converted',
                        ]);

                        Notification::make()
                            ->title('Property created')
                            ->body("Property #{$property->id} created from this lead.")
                            ->success()
                            ->send();
                    }),
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
            'index' => Pages\ListBuiltPropertyListingLeads::route('/'),
            'create' => Pages\CreateBuiltPropertyListingLead::route('/create'),
            'view' => Pages\ViewBuiltPropertyListingLead::route('/{record}'),
            'edit' => Pages\EditBuiltPropertyListingLead::route('/{record}/edit'),
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
