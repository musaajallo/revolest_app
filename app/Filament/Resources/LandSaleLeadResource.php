<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LandSaleLeadResource\Pages;
use App\Models\LandSaleLead;
use App\Models\Property;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LandSaleLeadResource extends Resource
{
    protected static ?string $model = LandSaleLead::class;

    protected static ?string $navigationGroup = 'Submissions';

    protected static ?string $navigationLabel = 'Land Sale Leads';

    protected static ?string $navigationIcon = 'heroicon-o-document-arrow-up';

    protected static ?int $navigationSort = 20;

    public static function getGlobalSearchResultUrl($record): string
    {
        return static::getUrl('view', ['record' => $record->getKey()]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['full_name', 'email', 'phone_primary', 'land_location'];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Contact')->columns(2)->schema([
                Forms\Components\TextInput::make('full_name')->required()->maxLength(255),
                Forms\Components\TextInput::make('email')->email()->maxLength(255),
                Forms\Components\TextInput::make('phone_primary')->required()->tel()->maxLength(50),
                Forms\Components\TextInput::make('phone_secondary')->tel()->maxLength(50),
                Forms\Components\TextInput::make('phone_tertiary')->tel()->maxLength(50),
                Forms\Components\TextInput::make('current_address')->maxLength(255),
            ]),
            Forms\Components\Section::make('Land Details')->columns(2)->schema([
                Forms\Components\Textarea::make('land_location')->columnSpanFull(),
                Forms\Components\TextInput::make('land_size')->maxLength(100),
                Forms\Components\Select::make('current_use')->options([
                    'residential' => 'Residential',
                    'commercial' => 'Commercial',
                    'agricultural' => 'Agricultural',
                    'vacant' => 'Vacant',
                    'other' => 'Other',
                ]),
                Forms\Components\TextInput::make('current_use_other')->maxLength(255),
                Forms\Components\Toggle::make('jointly_owned'),
                Forms\Components\Toggle::make('ownership_disputes'),
                Forms\Components\Select::make('zoning')->options([
                    'residential' => 'Residential',
                    'commercial' => 'Commercial',
                ]),
                Forms\Components\TextInput::make('asking_price')->numeric()->prefix('D'),
            ]),
            Forms\Components\Section::make('Consultation Purpose')->schema([
                Forms\Components\CheckboxList::make('consultation_purpose')->options([
                    'sell' => 'Selling the land',
                    'development' => 'Development opportunities',
                    'valuation' => 'Land valuation',
                    'environmental' => 'Environmental assessment',
                    'other' => 'Other',
                ])->columns(2),
                Forms\Components\TextInput::make('consultation_purpose_other')->maxLength(255),
                Forms\Components\Textarea::make('plans_for_land'),
                Forms\Components\Textarea::make('current_issues'),
            ]),
            Forms\Components\Section::make('Legal & Financial')->columns(2)->schema([
                Forms\Components\Toggle::make('has_liens'),
                Forms\Components\Toggle::make('taxes_up_to_date'),
                Forms\Components\Toggle::make('has_legal_documents'),
                Forms\Components\Toggle::make('free_from_disputes'),
                Forms\Components\CheckboxList::make('documents_provided')->options([
                    'title_deed' => 'Title Deed',
                    'tax_papers' => 'Tax Papers',
                    'physical_planning' => 'Physical Planning Document',
                    'lease_assignment' => 'Assignment of Lease',
                    'alkalo_transfer' => 'Alkalo Transfer',
                    'sketch_plan' => 'Sketch Plan',
                ])->columns(2)->columnSpanFull(),
            ]),
            Forms\Components\Section::make('Site Conditions')->columns(2)->schema([
                Forms\Components\CheckboxList::make('utilities')->options([
                    'electricity' => 'Electricity',
                    'sewage' => 'Sewage',
                    'water' => 'Water',
                    'none' => 'None',
                ])->columns(2)->columnSpanFull(),
                Forms\Components\Toggle::make('road_accessible'),
                Forms\Components\Toggle::make('has_recent_survey'),
                Forms\Components\Textarea::make('existing_structures')->columnSpanFull(),
                Forms\Components\Textarea::make('environmental_concerns')->columnSpanFull(),
                Forms\Components\Textarea::make('land_history')->columnSpanFull(),
            ]),
            Forms\Components\Section::make('Referral')->columns(2)->schema([
                Forms\Components\Toggle::make('previous_company_contact'),
                Forms\Components\Textarea::make('previous_company_experience')->columnSpanFull(),
                Forms\Components\TextInput::make('referral_source')->maxLength(255),
                Forms\Components\TextInput::make('referral_notes')->maxLength(255),
            ])->collapsed(),
            Forms\Components\Section::make('Triage')->columns(2)->schema([
                Forms\Components\Select::make('status')
                    ->options(array_combine(LandSaleLead::STATUSES, LandSaleLead::STATUSES))
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
                    Forms\Components\TextInput::make('bathrooms')->numeric()->minValue(0)->maxValue(20),
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
                Tables\Columns\TextColumn::make('full_name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('phone_primary')->label('Phone')->searchable(),
                Tables\Columns\TextColumn::make('land_size')->toggleable(),
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
                    ->options(array_combine(LandSaleLead::STATUSES, LandSaleLead::STATUSES)),
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
                    ->visible(fn (LandSaleLead $record) => $record->converted_property_id === null)
                    ->action(function (LandSaleLead $record): void {
                        $property = Property::create([
                            'title' => 'Land at '.($record->land_location ?? 'unspecified'),
                            'description' => $record->land_history,
                            'address' => $record->land_location ?? 'Unknown',
                            'price' => $record->asking_price ?? $record->budget_max ?? $record->budget_min ?? 0,
                            'sale_price' => $record->asking_price ?? $record->budget_max ?? $record->budget_min,
                            'type' => 'land',
                            'purpose' => 'sale',
                            'listing_category' => 'land',
                            'status' => 'inactive',
                            'bathrooms' => $record->bathrooms,
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
            'index' => Pages\ListLandSaleLeads::route('/'),
            'create' => Pages\CreateLandSaleLead::route('/create'),
            'view' => Pages\ViewLandSaleLead::route('/{record}'),
            'edit' => Pages\EditLandSaleLead::route('/{record}/edit'),
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
