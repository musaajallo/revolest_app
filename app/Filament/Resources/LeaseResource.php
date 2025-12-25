<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeaseResource\Pages;
use App\Filament\Resources\LeaseResource\RelationManagers;
use App\Models\Lease;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LeaseResource extends Resource
{
    protected static ?string $navigationGroup = 'Properties';
    public static function getGlobalSearchResultUrl($record): string
    {
        return static::getUrl('view', ['record' => $record->getKey()]);
    }
    public static function getGloballySearchableAttributes(): array
    {
        return ['property.title', 'tenant.name', 'status'];
    }
    public static function getGlobalSearchResultTitle($record): string
    {
        return ($record->property?->title ?? 'Lease') . ' - ' . ($record->tenant?->name ?? 'Unknown');
    }
    protected static ?string $model = Lease::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-check';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('property_id')
                    ->relationship('property', 'title')
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->searchable()
                    ->required(),
                Forms\Components\DatePicker::make('start_date')->required(),
                Forms\Components\DatePicker::make('end_date')->required(),
                Forms\Components\TextInput::make('rent_amount')
                    ->numeric()
                    ->required()
                    ->prefix('D')
                    ->label('Rent Amount (GMD)'),
                Forms\Components\TextInput::make('status')->required(),
                Forms\Components\TextInput::make('contract_file'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('property.title')->label('Property')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('tenant.name')->label('Tenant')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('start_date')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('end_date')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('rent_amount')
                    ->money('GMD')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('contract_file')->searchable()->sortable(),
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
            'index' => Pages\ListLeases::route('/'),
            'create' => Pages\CreateLease::route('/create'),
            'view' => Pages\ViewLease::route('/{record}'),
            'edit' => Pages\EditLease::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
