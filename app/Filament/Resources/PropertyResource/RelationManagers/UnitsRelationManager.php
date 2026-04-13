<?php

namespace App\Filament\Resources\PropertyResource\RelationManagers;

use App\Models\Listing;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class UnitsRelationManager extends RelationManager
{
    protected static string $relationship = 'listings';

    protected static ?string $title = 'Units';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('unit_name')
                ->label('Unit Name')
                ->required()
                ->maxLength(255),
            Forms\Components\Select::make('floor_number')
                ->label('Assigned Floor')
                ->options(fn (): array => $this->getFloorOptions())
                ->required()
                ->default(0)
                ->disabled(fn (): bool => ! (bool) $this->getOwnerRecord()->is_storey_building)
                ->dehydrateStateUsing(function ($state): int {
                    if (! $this->getOwnerRecord()->is_storey_building) {
                        return 0;
                    }

                    return (int) ($state ?? 0);
                }),
            Forms\Components\Toggle::make('listed_by_company')
                ->label('List directly under company')
                ->default(false)
                ->live(),
            Forms\Components\Select::make('agent_id')
                ->relationship('agent', 'name')
                ->searchable()
                ->hidden(fn (Get $get): bool => (bool) $get('listed_by_company'))
                ->required(fn (Get $get): bool => ! (bool) $get('listed_by_company')),
            Forms\Components\Select::make('status')
                ->label('Unit Type')
                ->options(fn (): array => $this->getStatusOptions())
                ->default(fn (): string => $this->getDefaultStatus())
                ->disabled(fn (): bool => ! $this->isMixedPurpose())
                ->dehydrated(true),
            Forms\Components\TextInput::make('price')
                ->numeric()
                ->prefix('D')
                ->label(fn (Get $get): string => $this->getPriceLabel($get))
                ->visible(fn (): bool => $this->isMixedPurpose())
                ->required(fn (Get $get): bool => $this->isMixedPurpose() && in_array($get('status'), ['for_rent', 'for_sale'], true)),
            Forms\Components\TextInput::make('security_deposit')
                ->numeric()
                ->prefix('D')
                ->label('Security Deposit (GMD)')
                ->visible(fn (Get $get): bool => $this->isMixedPurpose() && $get('status') === 'for_rent'),
            Forms\Components\TextInput::make('agent_fee')
                ->numeric()
                ->prefix('D')
                ->label('Agent Fee (GMD)')
                ->visible(fn (Get $get): bool => $this->isMixedPurpose() && $get('status') === 'for_rent'),
            Forms\Components\TextInput::make('area')
                ->numeric()
                ->minValue(0)
                ->label('Area (sq ft)'),
            Forms\Components\TextInput::make('bedrooms')
                ->numeric()
                ->minValue(0),
            Forms\Components\TextInput::make('bathrooms')
                ->numeric()
                ->minValue(0),
            Forms\Components\TextInput::make('guest_toilets')
                ->numeric()
                ->minValue(0)
                ->default(0)
                ->label('Guest Toilets'),
            Forms\Components\Toggle::make('has_dining_area')
                ->label('Dining Area')
                ->inline(false),
            Forms\Components\TextInput::make('boys_quarters')
                ->numeric()
                ->minValue(0)
                ->label('Boys Quarters'),
            Forms\Components\TextInput::make('kitchens')
                ->numeric()
                ->minValue(0)
                ->label('Kitchens'),
            Forms\Components\Toggle::make('has_guest_toilet')
                ->label('Guest Toilet')
                ->inline(false),
            Forms\Components\CheckboxList::make('amenities')
                ->options([
                    'ac' => 'AC',
                    'borehole' => 'Borehole',
                    'balcony' => 'Balcony',
                    'fitted_kitchen' => 'Fitted Kitchen',
                    'tiled_floors' => 'Tiled Floors',
                    'large_windows' => 'Large Windows',
                    'secure_compound' => 'Secure Compound',
                ])
                ->columns(2),
            Forms\Components\FileUpload::make('images')
                ->label('Unit Photos')
                ->multiple()
                ->maxFiles(15)
                ->reorderable()
                ->image()
                ->disk('public')
                ->directory('units')
                ->visibility('public')
                ->columnSpanFull(),
            Forms\Components\RichEditor::make('description')
                ->helperText('Mixed-use properties can have unit-level prices. Sale units use sale prices; rent units use rental prices with deposits and agent fees.')
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
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Units')
            ->recordAction('edit')
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('Add Unit'),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('unit_name')
                    ->label('Unit')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('floor_number')
                    ->label('Floor')
                    ->formatStateUsing(fn ($state): string => $this->getFloorLabel((int) $state)),
                Tables\Columns\TextColumn::make('bedrooms')->badge(),
                Tables\Columns\TextColumn::make('bathrooms')->badge(),
                Tables\Columns\TextColumn::make('guest_toilets')->label('Guest WC')->badge(),
                Tables\Columns\TextColumn::make('area')->label('Area')->suffix(' sqft')->sortable(),
                Tables\Columns\TextColumn::make('agent.name')
                    ->label('Listed By')
                    ->state(fn (Listing $record): string => $record->listed_by_company ? 'Company' : ($record->agent?->name ?? 'Company')),
                Tables\Columns\TextColumn::make('price')
                    ->label(fn (): string => $this->isMixedPurpose() ? 'Unit Price' : '—')
                    ->state(fn (Listing $record): ?float => $this->isMixedPurpose() ? $record->price : null)
                    ->money('GMD')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'for_sale',
                        'warning' => 'for_rent',
                        'gray' => 'rented',
                        'danger' => 'sold',
                    ]),
                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public function isReadOnly(): bool
    {
        return false;
    }

    protected function isMixedPurpose(): bool
    {
        return $this->getOwnerRecord()->purpose === 'mixed';
    }

    protected function getStatusOptions(): array
    {
        if (! $this->isMixedPurpose()) {
            return [
                $this->getDefaultStatus() => $this->getDefaultStatus() === 'for_sale' ? 'For Sale' : 'For Rent',
            ];
        }

        return [
            'for_rent' => 'For Rent',
            'for_sale' => 'For Sale',
            'rented' => 'Rented',
            'sold' => 'Sold',
        ];
    }

    protected function getDefaultStatus(): string
    {
        return match ($this->getOwnerRecord()->purpose) {
            'sale' => 'for_sale',
            'rent' => 'for_rent',
            default => 'for_rent',
        };
    }

    protected function getPriceLabel(Get $get): string
    {
        return $get('status') === 'for_rent' ? 'Rental Price (GMD)' : 'Sale Price (GMD)';
    }

    protected function getFloorOptions(): array
    {
        $property = $this->getOwnerRecord();

        if (! $property->is_storey_building) {
            return [0 => 'Ground Floor'];
        }

        $storeys = max(1, (int) ($property->number_of_storeys ?? 1));
        $options = [0 => 'Base Floor'];

        for ($i = 1; $i <= $storeys; $i++) {
            $options[$i] = $i . $this->ordinalSuffix($i) . ' Storey';
        }

        return $options;
    }

    protected function getFloorLabel(int $floorNumber): string
    {
        if ($floorNumber === 0) {
            return $this->getOwnerRecord()->is_storey_building ? 'Base Floor' : 'Ground Floor';
        }

        return $floorNumber . $this->ordinalSuffix($floorNumber) . ' Storey';
    }

    protected function ordinalSuffix(int $number): string
    {
        if (in_array($number % 100, [11, 12, 13], true)) {
            return 'th';
        }

        return match ($number % 10) {
            1 => 'st',
            2 => 'nd',
            3 => 'rd',
            default => 'th',
        };
    }
}
