<?php

namespace App\Filament\Widgets;

use App\Models\Lease;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class UpcomingLeaseExpirations extends BaseWidget
{
    protected static ?int $sort = 8;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Upcoming Lease Expirations (Next 30 Days)';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Lease::query()
                    ->where('status', 'active')
                    ->whereBetween('end_date', [Carbon::now(), Carbon::now()->addDays(30)])
                    ->orderBy('end_date', 'asc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('tenant.name')
                    ->label('Tenant')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('property.title')
                    ->label('Property')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Expires On')
                    ->date()
                    ->sortable()
                    ->color(fn ($record) => Carbon::parse($record->end_date)->diffInDays(Carbon::now()) <= 7 ? 'danger' : 'warning'),
                Tables\Columns\TextColumn::make('days_remaining')
                    ->label('Days Left')
                    ->state(fn ($record) => Carbon::now()->diffInDays(Carbon::parse($record->end_date)))
                    ->badge()
                    ->color(fn ($state) => $state <= 7 ? 'danger' : ($state <= 14 ? 'warning' : 'success')),
                Tables\Columns\TextColumn::make('rent_amount')
                    ->label('Rent (GMD)')
                    ->money('GMD')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->url(fn (Lease $record): string => route('filament.admin.resources.leases.view', $record))
                    ->icon('heroicon-o-eye'),
            ])
            ->emptyStateHeading('No expiring leases')
            ->emptyStateDescription('There are no leases expiring in the next 30 days.')
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}
