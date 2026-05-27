<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LandPurchaseLeadResource\Pages;
use App\Models\LandPurchaseLead;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LandPurchaseLeadResource extends Resource
{
    protected static ?string $model = LandPurchaseLead::class;

    protected static ?string $navigationGroup = 'Submissions';

    protected static ?string $navigationLabel = 'Land Purchase Leads';

    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static ?int $navigationSort = 10;

    public static function getGlobalSearchResultUrl($record): string
    {
        return static::getUrl('view', ['record' => $record->getKey()]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['full_name', 'email', 'phone'];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Contact')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('full_name')->required()->maxLength(255),
                    Forms\Components\TextInput::make('email')->email()->maxLength(255),
                    Forms\Components\TextInput::make('phone')->required()->tel()->maxLength(50),
                    Forms\Components\TextInput::make('address')->maxLength(255),
                    Forms\Components\TextInput::make('identification_type')->label('ID Type')->maxLength(100),
                    Forms\Components\TextInput::make('identification_number')->label('ID Number')->maxLength(100),
                    Forms\Components\Toggle::make('id_attached')->label('ID Document Attached'),
                ]),
            Forms\Components\Section::make('Plot Preferences')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('preferred_locations')->maxLength(255),
                    Forms\Components\TextInput::make('plot_size')->maxLength(100),
                    Forms\Components\TextInput::make('with_buildings')->label('With buildings or empty?')->maxLength(255),
                    Forms\Components\Toggle::make('future_development')->label('Wants future-dev potential'),
                    Forms\Components\Select::make('land_type')->options([
                        'residential' => 'Residential',
                        'commercial' => 'Commercial',
                        'agricultural' => 'Agricultural',
                        'recreation' => 'Recreation',
                        'industrial' => 'Industrial',
                    ]),
                ]),
            Forms\Components\Section::make('Budget & Timeline')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('budget')
                        ->label('Budget (free text)')
                        ->maxLength(100)
                        ->helperText('Legacy single field; prefer Min / Max below for new records.'),
                    Forms\Components\TextInput::make('budget_min')
                        ->numeric()->prefix('D')->label('Budget Min'),
                    Forms\Components\TextInput::make('budget_max')
                        ->numeric()->prefix('D')->label('Budget Max'),
                    Forms\Components\TextInput::make('payment_plan')->maxLength(100),
                    Forms\Components\Select::make('payment_method')->options([
                        'bank' => 'Bank',
                        'transfer' => 'Transfer',
                        'check' => 'Check',
                        'cash' => 'Cash',
                        'other' => 'Other',
                    ]),
                    Forms\Components\TextInput::make('timeframe')->maxLength(100),
                    Forms\Components\TextInput::make('completion_target')->maxLength(100),
                ]),
            Forms\Components\Section::make('Property Characteristics')
                ->columns(2)
                ->schema([
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
            Forms\Components\Section::make('Notes & Triage')
                ->columns(2)
                ->schema([
                    Forms\Components\Textarea::make('special_requirements')->columnSpanFull(),
                    Forms\Components\Textarea::make('notes')->label('Internal notes')->columnSpanFull(),
                    Forms\Components\Select::make('status')
                        ->options(array_combine(LandPurchaseLead::STATUSES, LandPurchaseLead::STATUSES))
                        ->required(),
                    Forms\Components\Select::make('agent_id')
                        ->label('Assigned Agent')
                        ->relationship('agent', 'name')
                        ->searchable(),
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
                Tables\Columns\TextColumn::make('phone')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('land_type')->badge()->toggleable(),
                Tables\Columns\TextColumn::make('budget')->toggleable(),
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
                    ->options(array_combine(LandPurchaseLead::STATUSES, LandPurchaseLead::STATUSES)),
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

    public static function getRelations(): array
    {
        return [
            \App\Filament\RelationManagers\LeadActivitiesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLandPurchaseLeads::route('/'),
            'create' => Pages\CreateLandPurchaseLead::route('/create'),
            'view' => Pages\ViewLandPurchaseLead::route('/{record}'),
            'edit' => Pages\EditLandPurchaseLead::route('/{record}/edit'),
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
}
