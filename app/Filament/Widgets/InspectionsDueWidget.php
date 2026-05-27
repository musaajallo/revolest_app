<?php

namespace App\Filament\Widgets;

use App\Models\Lease;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class InspectionsDueWidget extends BaseWidget
{
    protected static ?int $sort = 12;

    protected int | string | array $columnSpan = 1;

    protected static ?string $heading = 'Inspections Due — Next 30 Days';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Lease::query()
                    ->whereNotNull('next_inspection_at')
                    ->where('next_inspection_at', '<=', now()->addDays(30))
                    ->where('status', 'active')
                    ->orderBy('next_inspection_at')
                    ->with(['tenant', 'property'])
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('next_inspection_at')
                    ->label('Due')
                    ->date()
                    ->color(fn (Lease $record) => $record->next_inspection_at?->isPast() ? 'danger' : 'info')
                    ->sortable(),
                Tables\Columns\TextColumn::make('property.title')->label('Property')->limit(28),
                Tables\Columns\TextColumn::make('tenant.name')->label('Tenant')->limit(25),
                Tables\Columns\TextColumn::make('last_inspection_status')
                    ->label('Last')
                    ->badge()
                    ->color(fn (?string $state) => match ($state) {
                        'pass' => 'success',
                        'issues_found' => 'warning',
                        'fail' => 'danger',
                        'pending_followup' => 'info',
                        default => 'gray',
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('record')
                    ->label('Record')
                    ->icon('heroicon-o-plus')
                    ->url(fn (Lease $record): string => route('filament.admin.resources.inspections.create', ['lease_id' => $record->id])),
            ])
            ->paginated(false);
    }

    public static function canView(): bool
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        return $user && in_array($user->role, ['super_admin', 'admin', 'agent', 'owner']);
    }
}
