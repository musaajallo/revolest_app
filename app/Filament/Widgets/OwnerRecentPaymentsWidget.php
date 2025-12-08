<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class OwnerRecentPaymentsWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $user = Auth::user();
        $owner = $user?->owner ?? \App\Models\Owner::where('user_id', $owner?->id)->first();

        return $table
            ->heading('Recent Payments')
            ->query(
                Payment::query()
                    ->where('owner_id', $owner?->id)
                    ->with(['tenant', 'lease.property'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('tenant.name')
                    ->label('Tenant')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('lease.property.title')
                    ->label('Property')
                    ->limit(30)
                    ->searchable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money('GMD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_date')
                    ->label('Date')
                    ->date('M j, Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('method')
                    ->label('Method')
                    ->badge(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'completed',
                        'warning' => 'pending',
                        'danger' => 'failed',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('View')
                    ->url(fn (Payment $record): string => route('filament.admin.resources.payments.view', $record))
                    ->icon('heroicon-m-eye'),
            ]);
    }

    public static function canView(): bool
    {
        return Auth::user()?->role === 'owner';
    }
}
