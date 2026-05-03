<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PetApplicationResource\Pages;
use App\Models\PetApplication;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PetApplicationResource extends Resource
{
    protected static ?string $model = PetApplication::class;

    protected static ?string $navigationGroup = 'Submissions';

    protected static ?string $navigationLabel = 'Pet Applications';

    protected static ?string $navigationIcon = 'heroicon-o-heart';

    protected static ?int $navigationSort = 60;

    public static function getGlobalSearchResultUrl($record): string
    {
        return static::getUrl('view', ['record' => $record->getKey()]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['tenant_name', 'email', 'phone', 'property_address'];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Tenant & Property')->columns(2)->schema([
                Forms\Components\Select::make('tenant_id')->relationship('tenant', 'name')->searchable(),
                Forms\Components\Select::make('property_id')->relationship('property', 'title')->searchable(),
                Forms\Components\TextInput::make('tenant_name')->required()->maxLength(255),
                Forms\Components\TextInput::make('phone')->required()->tel()->maxLength(50),
                Forms\Components\TextInput::make('email')->email()->maxLength(255),
                Forms\Components\TextInput::make('property_address')->required()->maxLength(255),
                Forms\Components\DatePicker::make('lease_start_date'),
            ]),
            Forms\Components\Section::make('Pets')->schema([
                Forms\Components\Repeater::make('pets')
                    ->schema([
                        Forms\Components\TextInput::make('name')->maxLength(100),
                        Forms\Components\TextInput::make('type')->placeholder('dog, cat, bird, …')->maxLength(50),
                        Forms\Components\TextInput::make('breed')->maxLength(100),
                        Forms\Components\TextInput::make('age')->maxLength(50),
                        Forms\Components\TextInput::make('weight')->maxLength(50),
                        Forms\Components\Toggle::make('spayed_neutered'),
                        Forms\Components\Toggle::make('house_trained'),
                        Forms\Components\Toggle::make('vaccinations_up_to_date'),
                        Forms\Components\Toggle::make('aggression_history'),
                        Forms\Components\Textarea::make('aggression_notes')->columnSpanFull(),
                        Forms\Components\Toggle::make('special_medical_needs'),
                        Forms\Components\Textarea::make('medical_notes')->columnSpanFull(),
                    ])
                    ->columns(3)
                    ->minItems(1)
                    ->maxItems(5)
                    ->itemLabel(fn (array $state): ?string => ($state['name'] ?? null) ? ($state['name'].' ('.($state['type'] ?? '?').')') : null)
                    ->collapsible(),
            ]),
            Forms\Components\Section::make('Living Arrangements')->columns(2)->schema([
                Forms\Components\Select::make('keep_location')->options([
                    'indoors' => 'Indoors',
                    'outdoors' => 'Outdoors',
                    'both' => 'Both',
                ]),
                Forms\Components\Toggle::make('supervised_outdoors')->label('Supervised when outdoors'),
                Forms\Components\Toggle::make('past_complaints')->label('Past pet-related complaints / damages'),
                Forms\Components\Textarea::make('past_complaints_notes')->columnSpanFull(),
                Forms\Components\TextInput::make('emergency_contact_name')->maxLength(255),
                Forms\Components\TextInput::make('emergency_contact_phone')->tel()->maxLength(50),
            ]),
            Forms\Components\Section::make('Triage')->columns(2)->schema([
                Forms\Components\Select::make('status')
                    ->options(array_combine(PetApplication::STATUSES, array_map(fn ($s) => str_replace('_', ' ', ucfirst($s)), PetApplication::STATUSES)))
                    ->required(),
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
                Tables\Columns\TextColumn::make('tenant_name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('phone')->searchable(),
                Tables\Columns\TextColumn::make('property_address')->searchable()->limit(40),
                Tables\Columns\TextColumn::make('pet_count')
                    ->label('# Pets')
                    ->getStateUsing(fn (PetApplication $r) => is_array($r->pets) ? count($r->pets) : 0),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'warning',
                        'in_review' => 'info',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'closed' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('lease_start_date')->date()->toggleable(),
                Tables\Columns\TextColumn::make('submitted_at')->dateTime()->sortable()->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(array_combine(PetApplication::STATUSES, PetApplication::STATUSES)),
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
            'index' => Pages\ListPetApplications::route('/'),
            'create' => Pages\CreatePetApplication::route('/create'),
            'view' => Pages\ViewPetApplication::route('/{record}'),
            'edit' => Pages\EditPetApplication::route('/{record}/edit'),
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
