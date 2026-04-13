<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ListingResource\Pages;
use App\Models\Listing;
use App\Models\Property;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ListingResource extends Resource
{
    protected static ?string $navigationGroup = 'Properties';
    public static function getGlobalSearchResultUrl($record): string
    {
        return static::getUrl('view', ['record' => $record->getKey()]);
    }
    public static function getGloballySearchableAttributes(): array
    {
        return ['property.title', 'agent.name', 'status'];
    }
    public static function getGlobalSearchResultTitle($record): string
    {
        return $record->property?->title ?? 'Listing #' . $record->id;
    }
    protected static ?string $model = Listing::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('property_id')
                    ->relationship('property', 'title')
                    ->searchable()
                    ->live()
                    ->required(),
                Forms\Components\TextInput::make('unit_name')
                    ->label('Unit / Plot Name')
                    ->placeholder('e.g. Block A - Unit 2')
                    ->maxLength(255),
                Forms\Components\Toggle::make('listed_by_company')
                    ->label('List directly under company')
                    ->default(false)
                    ->live(),
                Forms\Components\Select::make('agent_id')
                    ->relationship('agent', 'name')
                    ->searchable()
                    ->hidden(fn (Get $get): bool => (bool) $get('listed_by_company'))
                    ->required(fn (Get $get): bool => !(bool) $get('listed_by_company')),
                Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->required()
                    ->prefix('D')
                    ->label('Price (GMD)'),
                Forms\Components\TextInput::make('security_deposit')
                    ->numeric()
                    ->prefix('D')
                    ->label('Security Deposit (GMD)'),
                Forms\Components\TextInput::make('agent_fee')
                    ->numeric()
                    ->prefix('D')
                    ->label('Agent Fee (GMD)'),
                Forms\Components\Select::make('status')
                    ->required()
                    ->options([
                        'for_rent' => 'For Rent',
                        'for_sale' => 'For Sale',
                        'rented' => 'Rented',
                        'sold' => 'Sold',
                    ])
                    ->default('for_rent'),
                Forms\Components\TextInput::make('bedrooms')
                    ->numeric()
                    ->minValue(0)
                    ->visible(fn (Get $get): bool => !static::selectedPropertyIsLand($get)),
                Forms\Components\TextInput::make('bathrooms')
                    ->numeric()
                    ->minValue(0)
                    ->visible(fn (Get $get): bool => !static::selectedPropertyIsLand($get)),
                Forms\Components\Toggle::make('has_dining_area')
                    ->label('Dining Area')
                    ->inline(false)
                    ->visible(fn (Get $get): bool => !static::selectedPropertyIsLand($get)),
                Forms\Components\TextInput::make('boys_quarters')
                    ->numeric()
                    ->minValue(0)
                    ->label('Boys Quarters')
                    ->visible(fn (Get $get): bool => !static::selectedPropertyIsLand($get)),
                Forms\Components\TextInput::make('kitchens')
                    ->numeric()
                    ->minValue(0)
                    ->label('Kitchens')
                    ->visible(fn (Get $get): bool => !static::selectedPropertyIsLand($get)),
                Forms\Components\Toggle::make('has_guest_toilet')
                    ->label('Guest Toilet')
                    ->inline(false)
                    ->visible(fn (Get $get): bool => !static::selectedPropertyIsLand($get)),
                Forms\Components\CheckboxList::make('amenities')
                    ->options([
                        'ac' => 'AC',
                        'borehole' => 'Borehole',
                    ])
                    ->columns(2),
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
                Forms\Components\DateTimePicker::make('published_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('property.title')->label('Property')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('agent.name')
                    ->label('Listed By')
                    ->state(fn (Listing $record): string => $record->listed_by_company ? 'Company' : ($record->agent?->name ?? 'Company'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('GMD')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('security_deposit')->money('GMD')->label('Deposit')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('agent_fee')->money('GMD')->label('Agent Fee')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('published_at')->searchable()->sortable(),
            ])
            ->filters([
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
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListListings::route('/'),
            'create' => Pages\CreateListing::route('/create'),
            'view' => Pages\ViewListing::route('/{record}'),
            'edit' => Pages\EditListing::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    protected static function selectedPropertyIsLand(Get $get): bool
    {
        $propertyId = $get('property_id');

        if (!$propertyId) {
            return false;
        }

        return Property::query()->whereKey($propertyId)->value('type') === 'land';
    }
}
