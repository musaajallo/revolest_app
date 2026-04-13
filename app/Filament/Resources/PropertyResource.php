<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PropertyResource\Pages;
use App\Filament\Resources\PropertyResource\RelationManagers\UnitsRelationManager;
use App\Models\Property;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PropertyResource extends Resource
{
    protected static ?string $navigationGroup = 'Properties';
    public static function getGlobalSearchResultUrl($record): string
    {
        return static::getUrl('view', ['record' => $record->getKey()]);
    }
    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'address'];
    }
    protected static ?string $model = Property::class;

    protected static ?string $navigationIcon = 'heroicon-o-home';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')->required(),
                Forms\Components\Select::make('owner_id')
                    ->relationship('owner', 'name')
                    ->searchable()
                    ->nullable(),
                Forms\Components\TextInput::make('address')
                    ->label('Location')
                    ->required(),
                Forms\Components\TextInput::make('plus_code')
                    ->label('Plus Code')
                    ->helperText('Google Plus Code or similar location code')
                    ->maxLength(255),
                Forms\Components\Select::make('type')
                    ->required()
                    ->options([
                        'house' => 'House',
                        'apartment' => 'Apartment',
                        'compound' => 'Compound',
                        'duplex' => 'Duplex',
                        'commercial' => 'Commercial',
                        'land' => 'Land',
                    ])
                    ->live()
                    ->afterStateUpdated(function (Forms\Set $set, ?string $state): void {
                        $set('listing_category', $state === 'land' ? 'land' : 'building');
                    }),
                Forms\Components\Select::make('purpose')
                    ->label('Purpose')
                    ->required()
                    ->options([
                        'sale' => 'For Sale',
                        'rent' => 'For Rent',
                        'mixed' => 'Mixed',
                    ])
                    ->default('mixed')
                    ->live(),
                Forms\Components\Hidden::make('listing_category')
                    ->default('building')
                    ->dehydrateStateUsing(fn (?string $state, callable $get): string => $get('type') === 'land' ? 'land' : ($state ?: 'building')),
                Forms\Components\TextInput::make('sale_price')
                    ->numeric()
                    ->prefix('D')
                    ->label('Sale Price (GMD)')
                    ->visible(fn (Forms\Get $get): bool => $get('purpose') === 'sale')
                    ->required(fn (Forms\Get $get): bool => $get('purpose') === 'sale'),
                Forms\Components\TextInput::make('rental_price')
                    ->numeric()
                    ->prefix('D')
                    ->label('Yearly Rent (GMD)')
                    ->visible(fn (Forms\Get $get): bool => $get('purpose') === 'rent')
                    ->required(fn (Forms\Get $get): bool => $get('purpose') === 'rent'),
                Forms\Components\TextInput::make('security_deposit')
                    ->numeric()
                    ->prefix('D')
                    ->label('Security Deposit (GMD)')
                    ->visible(fn (Forms\Get $get): bool => $get('purpose') === 'rent'),
                Forms\Components\TextInput::make('agent_fee')
                    ->numeric()
                    ->prefix('D')
                    ->label('Agent Fee (GMD)')
                    ->visible(fn (Forms\Get $get): bool => $get('purpose') === 'rent'),
                Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->prefix('D')
                    ->label('Base Price (GMD)')
                    ->helperText('Used for sale properties only.')
                    ->visible(fn (Forms\Get $get): bool => $get('purpose') === 'sale')
                    ->required(fn (Forms\Get $get): bool => $get('purpose') === 'sale'),
                Forms\Components\Select::make('status')
                    ->required()
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ])
                    ->default('inactive'),
                Forms\Components\Toggle::make('is_featured')
                    ->label('Featured on Homepage')
                    ->helperText('Maximum 10 featured properties are allowed at a time.')
                    ->disabled(function (?Property $record): bool {
                        if ($record?->is_featured) {
                            return false;
                        }

                        return Property::query()->where('is_featured', true)->count() >= 10;
                    }),
                Forms\Components\DateTimePicker::make('available_from')
                    ->label('Available On Website From')
                    ->native(false)
                    ->displayFormat('Y-m-d H:i')
                    ->seconds(false)
                    ->helperText('Leave empty for immediate availability.'),
                Forms\Components\Toggle::make('is_storey_building')
                    ->label('Is Storey Building')
                    ->live(),
                Forms\Components\TextInput::make('number_of_storeys')
                    ->label('Number of Storeys')
                    ->numeric()
                    ->minValue(1)
                    ->visible(fn (Forms\Get $get): bool => (bool) $get('is_storey_building'))
                    ->required(fn (Forms\Get $get): bool => (bool) $get('is_storey_building')),
                Forms\Components\TextInput::make('area')->numeric(),
                Forms\Components\RichEditor::make('description')
                    ->toolbarButtons([
                        'bold',
                        'italic',
                        'underline',
                        'bulletList',
                        'orderedList',
                        'h2',
                        'h3',
                        'link',
                        'blockquote',
                        'undo',
                        'redo',
                    ])
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('images')
                    ->label('Property Images')
                    ->multiple()
                    ->maxFiles(15)
                    ->reorderable()
                    ->image()
                    ->disk('public')
                    ->directory('properties')
                    ->visibility('public')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('address')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('owner.name')->label('Owner')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('purpose')->label('Purpose')->badge()->sortable(),
                Tables\Columns\TextColumn::make('units_count')->label('Units')->state(fn (Property $record): int => $record->units_count)->sortable(),
                Tables\Columns\TextColumn::make('type')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('status')->searchable()->sortable(),
                Tables\Columns\IconColumn::make('is_featured')->boolean()->label('Featured'),
                Tables\Columns\TextColumn::make('available_from')->dateTime()->label('Available From')->sortable(),
                Tables\Columns\TextColumn::make('area')->searchable()->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('manage_units')
                    ->label('Manage Units')
                    ->icon('heroicon-o-building-office-2')
                    ->color('primary')
                    ->url(fn (Property $record): string => static::getUrl('view', ['record' => $record])),
                Tables\Actions\ViewAction::make()->color('success'),
                Tables\Actions\EditAction::make()->color('warning'),
                Tables\Actions\DeleteAction::make()->color('danger'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            UnitsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProperties::route('/'),
            'create' => Pages\CreateProperty::route('/create'),
            'view' => Pages\ViewProperty::route('/{record}'),
            'edit' => Pages\EditProperty::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['listings' => fn ($query) => $query->latest()])
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
