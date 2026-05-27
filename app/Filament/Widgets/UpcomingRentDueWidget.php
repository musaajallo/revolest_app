<?php

namespace App\Filament\Widgets;

use App\Models\Lease;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class UpcomingRentDueWidget extends BaseWidget
{
    protected static ?int $sort = 11;

    protected int | string | array $columnSpan = 1;

    protected static ?string $heading = 'Rent Due — Next 14 Days';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Lease::query()
                    ->whereNotNull('next_rent_due_at')
                    ->where('next_rent_due_at', '<=', now()->addDays(14))
                    ->where('status', 'active')
                    ->orderBy('next_rent_due_at')
                    ->with(['tenant', 'property'])
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('next_rent_due_at')
                    ->label('Due')
                    ->date()
                    ->color(fn (Lease $record) => $record->next_rent_due_at?->isPast() ? 'danger' : 'warning')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tenant.name')->label('Tenant')->limit(25),
                Tables\Columns\TextColumn::make('property.title')->label('Property')->limit(28),
                Tables\Columns\TextColumn::make('rent_amount')->money('GMD'),
                Tables\Columns\TextColumn::make('rent_cycle')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => $state ? (Lease::RENT_CYCLES[$state] ?? $state) : null),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->url(fn (Lease $record): string => route('filament.admin.resources.leases.view', $record))
                    ->icon('heroicon-o-eye'),
            ])
            ->paginated(false);
    }

    public static function canView(): bool
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        return $user && in_array($user->role, ['super_admin', 'admin', 'agent', 'owner']);
    }
}
