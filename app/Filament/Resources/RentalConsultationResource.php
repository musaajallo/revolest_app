<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RentalConsultationResource\Pages;
use App\Models\RentalConsultation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RentalConsultationResource extends Resource
{
    protected static ?string $model = RentalConsultation::class;

    protected static ?string $navigationGroup = 'Submissions';

    protected static ?string $navigationLabel = 'Rental Consultations';

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?int $navigationSort = 30;

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
            Forms\Components\Section::make('General Information')->columns(2)->schema([
                Forms\Components\DatePicker::make('consultation_date'),
                Forms\Components\TextInput::make('full_name')->required()->maxLength(255),
                Forms\Components\TextInput::make('address')->maxLength(255),
                Forms\Components\TextInput::make('nationality')->maxLength(100),
                Forms\Components\TextInput::make('occupation')->maxLength(100),
                Forms\Components\TextInput::make('institution')->maxLength(255),
                Forms\Components\TextInput::make('marital_status')->maxLength(50),
                Forms\Components\TextInput::make('number_of_kids')->numeric()->minValue(0),
                Forms\Components\TextInput::make('phone')->required()->tel()->maxLength(50),
                Forms\Components\TextInput::make('email')->email()->maxLength(255),
            ]),
            Forms\Components\Section::make('Property Details')->columns(2)->schema([
                Forms\Components\TextInput::make('preferred_areas')->maxLength(255),
                Forms\Components\Select::make('property_kind')->options([
                    'full_compound' => 'Full Compound',
                    'apartment' => 'Apartment',
                ]),
                Forms\Components\TextInput::make('bedrooms')->numeric()->minValue(0)->maxValue(20),
                Forms\Components\Toggle::make('furnished'),
                Forms\Components\TextInput::make('preferred_structure')->maxLength(255),
                Forms\Components\Textarea::make('desired_facilities')->columnSpanFull(),
                Forms\Components\Textarea::make('property_suggestions')->columnSpanFull(),
                Forms\Components\Textarea::make('reason_for_moving')->columnSpanFull(),
            ]),
            Forms\Components\Section::make('Tenancy & Payment')->columns(2)->schema([
                Forms\Components\TextInput::make('occupants_count')->numeric()->minValue(1),
                Forms\Components\TextInput::make('move_in_window')->maxLength(100),
                Forms\Components\TextInput::make('rental_duration')->maxLength(100),
                Forms\Components\TextInput::make('payment_plan')->maxLength(255),
                Forms\Components\Select::make('payment_method')->options([
                    'cash' => 'Cash',
                    'bank_transfer' => 'Bank Transfer',
                    'cheque' => 'Cheque',
                ]),
                Forms\Components\Select::make('payer')->options([
                    'me' => 'Me',
                    'other' => 'Other',
                ]),
                Forms\Components\TextInput::make('payer_name')->maxLength(255),
                Forms\Components\TextInput::make('payer_occupation')->maxLength(255),
                Forms\Components\TextInput::make('payer_address')->maxLength(255),
                Forms\Components\TextInput::make('payer_phone')->tel()->maxLength(50),
                Forms\Components\TextInput::make('payer_relationship')->maxLength(100),
            ]),
            Forms\Components\Section::make('Referral')->columns(2)->schema([
                Forms\Components\Toggle::make('previous_company_contact'),
                Forms\Components\Textarea::make('previous_company_experience')->columnSpanFull(),
                Forms\Components\TextInput::make('referral_source')->maxLength(255),
                Forms\Components\TextInput::make('referral_name')->maxLength(255),
            ])->collapsed(),
            Forms\Components\Section::make('Triage')->columns(2)->schema([
                Forms\Components\Select::make('status')
                    ->options(array_combine(RentalConsultation::STATUSES, RentalConsultation::STATUSES))
                    ->required(),
                Forms\Components\Select::make('agent_id')->relationship('agent', 'name')->searchable(),
                Forms\Components\Select::make('tenant_id')->relationship('tenant', 'name')->searchable(),
                Forms\Components\Textarea::make('notes')->label('Internal notes')->columnSpanFull(),
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
                Tables\Columns\TextColumn::make('phone')->searchable(),
                Tables\Columns\TextColumn::make('preferred_areas')->toggleable(),
                Tables\Columns\TextColumn::make('property_kind')->badge()->toggleable(),
                Tables\Columns\IconColumn::make('furnished')->boolean()->toggleable(),
                Tables\Columns\TextColumn::make('move_in_window')->toggleable(),
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
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(array_combine(RentalConsultation::STATUSES, RentalConsultation::STATUSES)),
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
            'index' => Pages\ListRentalConsultations::route('/'),
            'create' => Pages\CreateRentalConsultation::route('/create'),
            'view' => Pages\ViewRentalConsultation::route('/{record}'),
            'edit' => Pages\EditRentalConsultation::route('/{record}/edit'),
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
