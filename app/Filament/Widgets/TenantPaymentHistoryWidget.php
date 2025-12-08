<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class TenantPaymentHistoryWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $user = Auth::user();
        $tenant = $user?->tenant ?? \App\Models\Tenant::where('user_id', $user?->id)->first();

        return $table
            ->heading('Payment History')
            ->query(
                Payment::query()
                    ->where('tenant_id', $tenant?->id)
                    ->with(['lease.property'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('lease.property.title')
                    ->label('Property')
                    ->limit(40)
                    ->searchable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money('GMD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_date')
                    ->label('Payment Date')
                    ->date('M j, Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('method')
                    ->label('Method')
                    ->badge()
                    ->colors([
                        'primary' => 'cash',
                        'success' => 'bank_transfer',
                        'info' => 'mobile_money',
                        'warning' => 'check',
                    ]),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'completed',
                        'warning' => 'pending',
                        'danger' => 'failed',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('view_receipt')
                    ->label('Receipt')
                    ->url(fn (Payment $record): string =>
                        $record->receipt ? route('filament.admin.resources.receipts.view', $record->receipt) : '#'
                    )
                    ->icon('heroicon-m-document-text')
                    ->visible(fn (Payment $record) => $record->receipt !== null),
            ]);
    }

    public static function canView(): bool
    {
        return Auth::user()?->role === 'tenant';
    }
}
