<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OwnerResource\Pages;
use App\Models\Owner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OwnerResource extends Resource
{
    protected static ?string $navigationGroup = 'Management';
    public static function getGlobalSearchResultUrl($record): string
    {
        return static::getUrl('view', ['record' => $record->getKey()]);
    }
    public static function getGlobalSearchResultTitle($record): string
    {
        return $record->unique_id . ' - ' . $record->name;
    }
    public static function getGloballySearchableAttributes(): array
    {
        return ['unique_id', 'name', 'email'];
    }
    protected static ?string $model = Owner::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Identity')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('unique_id')
                            ->label('Owner ID')
                            ->disabled()
                            ->dehydrated(false)
                            ->visible(fn ($record) => $record !== null),
                        Forms\Components\TextInput::make('name')->required(),
                        Forms\Components\TextInput::make('email')->email()->required(),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->placeholder('+220 123 4567'),
                        Forms\Components\Textarea::make('bio')->columnSpanFull(),
                        Forms\Components\FileUpload::make('photo')
                            ->label('Owner Photo')
                            ->image()
                            ->disk('public')
                            ->directory('owners')
                            ->visibility('public')
                            ->columnSpanFull(),
                        Forms\Components\Select::make('user_id')
                            ->label('Linked Login User')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->helperText('The User account this owner uses to log in. Required for owners who need access to /admin.')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Bank Details')
                    ->description('Account where rent payouts and balances are remitted.')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('bank_name'),
                        Forms\Components\TextInput::make('bank_branch'),
                        Forms\Components\TextInput::make('bank_account_name'),
                        Forms\Components\TextInput::make('bank_account_number'),
                    ]),

                Forms\Components\Section::make('Commission')
                    ->description("Revolest's % cut on this owner's rent collections. Can be overridden per lease.")
                    ->schema([
                        Forms\Components\TextInput::make('commission_percent')
                            ->label('Commission %')
                            ->numeric()
                            ->suffix('%')
                            ->default(10.00)
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.01)
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('unique_id')
                    ->label('Owner ID')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('phone')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('commission_percent')
                    ->label('Commission')
                    ->suffix('%')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('bank_name')
                    ->label('Bank')
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListOwners::route('/'),
            'create' => Pages\CreateOwner::route('/create'),
            'view' => Pages\ViewOwner::route('/{record}'),
            'edit' => Pages\EditOwner::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function canAccess(): bool
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        if (!$user) {
            return false;
        }

        return in_array($user->role, ['super_admin', 'admin']);
    }
}
