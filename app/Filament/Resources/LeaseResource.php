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
                Forms\Components\Section::make('Parties & Term')
                    ->columns(2)
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
                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'pending' => 'Pending',
                                'expired' => 'Expired',
                                'terminated' => 'Terminated',
                            ])
                            ->default('active')
                            ->required(),
                        Forms\Components\FileUpload::make('contract_file')
                            ->disk('public')
                            ->directory('contracts')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Security Deposit')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('security_deposit_amount')
                            ->numeric()
                            ->prefix('D')
                            ->label('Deposit Amount (GMD)'),
                        Forms\Components\Select::make('security_deposit_status')
                            ->options(\App\Models\Lease::DEPOSIT_STATUSES)
                            ->default('pending')
                            ->required(),
                    ]),

                Forms\Components\Section::make('Rent Schedule')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('rent_cycle')
                            ->options(\App\Models\Lease::RENT_CYCLES)
                            ->default('annually')
                            ->required(),
                        Forms\Components\DatePicker::make('next_rent_due_at')
                            ->label('Next Rent Due')
                            ->helperText('Auto-computed from start date + cycle if left blank.'),
                        Forms\Components\TextInput::make('commission_percent_override')
                            ->label('Commission % Override')
                            ->numeric()
                            ->suffix('%')
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.01)
                            ->helperText("Leave blank to use the owner's default rate."),
                    ]),

                Forms\Components\Section::make('Inspections')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('inspection_cycle_months')
                            ->label('Inspection Cycle (months)')
                            ->numeric()
                            ->default(6)
                            ->minValue(1)
                            ->maxValue(36),
                        Forms\Components\DatePicker::make('next_inspection_at')->label('Next Inspection'),
                        Forms\Components\DatePicker::make('last_inspection_at')
                            ->label('Last Inspection')
                            ->disabled()
                            ->helperText('Updated automatically when an inspection is recorded.'),
                        Forms\Components\TextInput::make('last_inspection_status')
                            ->label('Last Status')
                            ->disabled(),
                    ]),

                Forms\Components\Section::make('Notes')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('property.title')->label('Property')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('tenant.name')->label('Tenant')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('start_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('end_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('rent_amount')->money('GMD')->sortable(),
                Tables\Columns\TextColumn::make('rent_cycle')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => $state ? (\App\Models\Lease::RENT_CYCLES[$state] ?? $state) : null)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('next_rent_due_at')->label('Next Due')->date()->sortable(),
                Tables\Columns\TextColumn::make('security_deposit_status')
                    ->label('Deposit')
                    ->badge()
                    ->color(fn (?string $state) => match ($state) {
                        'paid' => 'success',
                        'partial' => 'warning',
                        'pending' => 'gray',
                        'forfeited' => 'danger',
                        'refunded' => 'info',
                        default => 'gray',
                    })
                    ->toggleable(),
                Tables\Columns\TextColumn::make('next_inspection_at')->label('Next Inspect.')->date()->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (?string $state) => match ($state) {
                        'active' => 'success',
                        'pending' => 'warning',
                        'expired' => 'gray',
                        'terminated' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
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

    public static function canAccess(): bool
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        return $user && in_array($user->role, ['super_admin', 'admin', 'agent', 'owner', 'tenant']);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);

        $user = \Illuminate\Support\Facades\Auth::user();
        if (! $user || in_array($user->role, ['super_admin', 'admin'])) {
            return $query;
        }

        if ($user->role === 'tenant') {
            return $query->where('tenant_id', $user->tenant?->id ?? 0);
        }

        if ($user->role === 'owner') {
            $ownerId = $user->owner?->id ?? 0;
            return $query->whereHas('property', fn ($q) => $q->where('owner_id', $ownerId));
        }

        if ($user->role === 'agent') {
            $agentId = $user->agent?->id ?? 0;
            return $query->whereHas('property.listings', fn ($q) => $q->where('agent_id', $agentId));
        }

        return $query->whereRaw('1=0');
    }
}
