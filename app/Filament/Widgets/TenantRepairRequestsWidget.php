<?php

namespace App\Filament\Widgets;

use App\Models\RepairRequest;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class TenantRepairRequestsWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $user = Auth::user();
        $tenant = $user?->tenant ?? \App\Models\Tenant::where('user_id', $user?->id)->first();

        return $table
            ->heading('My Repair Requests')
            ->query(
                RepairRequest::query()
                    ->where('tenant_id', $tenant?->id)
                    ->with(['property'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('property.title')
                    ->label('Property')
                    ->limit(40)
                    ->searchable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->limit(50)
                    ->wrap(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'info' => 'in_progress',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                    ]),

                Tables\Columns\TextColumn::make('submitted_at')
                    ->label('Submitted')
                    ->dateTime('M j, Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('resolved_at')
                    ->label('Resolved')
                    ->dateTime('M j, Y')
                    ->placeholder('Pending')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('View')
                    ->url(fn (RepairRequest $record): string => route('filament.admin.resources.repair-requests.view', $record))
                    ->icon('heroicon-m-eye'),
            ]);
    }

    public static function canView(): bool
    {
        return Auth::user()?->role === 'tenant';
    }
}
