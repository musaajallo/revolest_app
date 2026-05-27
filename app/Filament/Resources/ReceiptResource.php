<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReceiptResource\Pages;
use App\Filament\Resources\ReceiptResource\RelationManagers;
use App\Models\Receipt;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReceiptResource extends Resource
{
    protected static ?string $navigationGroup = 'Properties';
    public static function getGlobalSearchResultUrl($record): string
    {
        return static::getUrl('view', ['record' => $record->getKey()]);
    }
    public static function getGloballySearchableAttributes(): array
    {
        return ['receipt_number', 'description'];
    }

    public static function getGlobalSearchResultTitle($record): string
    {
        return $record->receipt_number ?? ('Receipt #' . $record->id);
    }
    protected static ?string $model = Receipt::class;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-refund';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('receipt_number')
                    ->label('Receipt #')
                    ->disabled()
                    ->dehydrated(false)
                    ->visible(fn ($record) => $record !== null)
                    ->helperText('Auto-generated on create (RCV-{year}-{sequence}).'),
                Forms\Components\Select::make('payment_id')
                    ->relationship('payment', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record) => 'Payment #' . $record->id . ' — D' . number_format((float) $record->amount, 2))
                    ->searchable()
                    ->required(),
                Forms\Components\DateTimePicker::make('issued_at')->required()->default(now()),
                Forms\Components\TextInput::make('amount')->numeric()->prefix('D')->required(),
                Forms\Components\FileUpload::make('file')
                    ->label('Receipt File')
                    ->disk('public')
                    ->directory('receipts'),
                Forms\Components\Textarea::make('description')->rows(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('receipt_number')
                    ->label('Receipt #')
                    ->searchable()
                    ->copyable()
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('issued_at')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('payment.tenant.name')->label('Tenant')->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('amount')->money('GMD')->sortable(),
                Tables\Columns\TextColumn::make('description')->limit(40)->toggleable(),
            ])
            ->defaultSort('issued_at', 'desc')
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
            'index' => Pages\ListReceipts::route('/'),
            'create' => Pages\CreateReceipt::route('/create'),
            'view' => Pages\ViewReceipt::route('/{record}'),
            'edit' => Pages\EditReceipt::route('/{record}/edit'),
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
            $tenantId = $user->tenant?->id ?? 0;
            return $query->whereHas('payment', fn ($q) => $q->where('tenant_id', $tenantId));
        }

        if ($user->role === 'owner') {
            $ownerId = $user->owner?->id ?? 0;
            return $query->whereHas('payment', fn ($q) => $q->where('owner_id', $ownerId));
        }

        if ($user->role === 'agent') {
            $agentId = $user->agent?->id ?? 0;
            return $query->whereHas(
                'payment.lease.property.listings',
                fn ($q) => $q->where('agent_id', $agentId)
            );
        }

        return $query->whereRaw('1=0');
    }
}
