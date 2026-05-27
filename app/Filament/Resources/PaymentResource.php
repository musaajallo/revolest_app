<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Filament\Resources\PaymentResource\RelationManagers;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaymentResource extends Resource
{
    protected static ?string $navigationGroup = 'Properties';
    public static function getGlobalSearchResultUrl($record): string
    {
        return static::getUrl('view', ['record' => $record->getKey()]);
    }
    public static function getGloballySearchableAttributes(): array
    {
        return ['status'];
    }
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Parties')
                    ->columns(3)
                    ->schema([
                        Forms\Components\Select::make('lease_id')
                            ->relationship('lease', 'id')
                            ->getOptionLabelFromRecordUsing(fn ($record) => ($record->property?->title ?? 'Property')
                                . ' — ' . ($record->tenant?->name ?? 'Tenant'))
                            ->searchable(['property.title', 'tenant.name'])
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $lease = \App\Models\Lease::with('property')->find($state);
                                    if ($lease) {
                                        $set('tenant_id', $lease->tenant_id);
                                        $set('owner_id', $lease->property?->owner_id);
                                        if ($lease->rent_amount) {
                                            $set('expected_amount', $lease->rent_amount);
                                        }
                                    }
                                }
                            }),
                        Forms\Components\Select::make('tenant_id')
                            ->relationship('tenant', 'name')
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('owner_id')
                            ->relationship('owner', 'name')
                            ->searchable()
                            ->required(),
                    ]),

                Forms\Components\Section::make('Payment')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('purpose')
                            ->options(\App\Models\Payment::PURPOSES)
                            ->default('rent')
                            ->required(),
                        Forms\Components\DatePicker::make('payment_date')
                            ->default(now())
                            ->required(),
                        Forms\Components\TextInput::make('amount')
                            ->numeric()
                            ->required()
                            ->prefix('D')
                            ->label('Amount Paid (GMD)'),
                        Forms\Components\TextInput::make('expected_amount')
                            ->numeric()
                            ->prefix('D')
                            ->label('Expected (GMD)')
                            ->helperText('Defaulted from lease rent when a lease is selected.'),
                        Forms\Components\Select::make('method')
                            ->options([
                                'cash' => 'Cash',
                                'bank_transfer' => 'Bank Transfer',
                                'cheque' => 'Cheque',
                                'mobile_money' => 'Mobile Money',
                                'card' => 'Card',
                                'other' => 'Other',
                            ])
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options(\App\Models\Payment::STATUSES)
                            ->default('complete')
                            ->required(),
                    ]),

                Forms\Components\Section::make('Period Covered')
                    ->columns(3)
                    ->collapsible()
                    ->schema([
                        Forms\Components\DatePicker::make('period_start')->label('From'),
                        Forms\Components\DatePicker::make('period_end')->label('To'),
                        Forms\Components\TextInput::make('period_label')
                            ->placeholder('e.g. Jan 2026, Q1 2026, 2026 annual'),
                    ]),

                Forms\Components\Section::make('Attribution')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('paid_by_name')
                            ->label('Paid by')
                            ->helperText('Name of the actual payer if different from the tenant (e.g. employer, parent, guarantor).'),
                        Forms\Components\Select::make('received_by_user_id')
                            ->label('Received by')
                            ->relationship('receivedBy', 'name')
                            ->searchable(),
                        Forms\Components\TextInput::make('commission_amount')
                            ->numeric()
                            ->prefix('D')
                            ->label('Commission (GMD)')
                            ->helperText('Auto-calculated on create from owner commission %; override here to lock a different amount.'),
                    ]),

                Forms\Components\Section::make('Attachment & Notes')
                    ->schema([
                        Forms\Components\FileUpload::make('receipt_file')
                            ->label('Proof / Bank slip')
                            ->disk('public')
                            ->directory('payment-proofs'),
                        Forms\Components\Textarea::make('notes')->rows(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('receipt.receipt_number')
                    ->label('Receipt #')
                    ->searchable()
                    ->copyable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('payment_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('tenant.name')->label('Tenant')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('lease.property.title')->label('Property')->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('purpose')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => $state ? (\App\Models\Payment::PURPOSES[$state] ?? $state) : null),
                Tables\Columns\TextColumn::make('amount')->money('GMD')->sortable(),
                Tables\Columns\TextColumn::make('expected_amount')
                    ->label('Expected')
                    ->money('GMD')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('outstandingBalance')
                    ->label('Outstanding')
                    ->money('GMD')
                    ->getStateUsing(fn ($record) => $record->outstandingBalance())
                    ->color(fn ($state) => $state !== null && $state > 0 ? 'danger' : 'gray')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('commission_amount')
                    ->label('CMS Earn')
                    ->money('GMD')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('method')
                    ->badge()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('paid_by_name')->label('Paid by')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('receivedBy.name')->label('Received by')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (?string $state) => match ($state) {
                        'complete' => 'success',
                        'pending' => 'warning',
                        'incomplete' => 'danger',
                        'failed' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
            ])
            ->defaultSort('payment_date', 'desc')
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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'view' => Pages\ViewPayment::route('/{record}'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
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
            return $query->where('owner_id', $user->owner?->id ?? 0);
        }

        if ($user->role === 'agent') {
            $agentId = $user->agent?->id ?? 0;
            return $query->whereHas('lease.property.listings', fn ($q) => $q->where('agent_id', $agentId));
        }

        return $query->whereRaw('1=0');
    }
}
