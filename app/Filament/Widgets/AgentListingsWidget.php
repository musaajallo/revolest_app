<?php

namespace App\Filament\Widgets;

use App\Models\Listing;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class AgentListingsWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $user = Auth::user();
        $agent = $user?->agent ?? \App\Models\Agent::where('user_id', $user?->id)->first();

        return $table
            ->heading('My Recent Listings')
            ->query(
                Listing::query()
                    ->where('agent_id', $agent?->id)
                    ->with(['property'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('property.title')
                    ->label('Property')
                    ->searchable()
                    ->sortable()
                    ->limit(40),

                Tables\Columns\TextColumn::make('property.address')
                    ->label('Address')
                    ->limit(30)
                    ->searchable(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Price')
                    ->money('GMD')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'pending',
                        'danger' => 'inactive',
                        'gray' => 'rented',
                    ]),

                Tables\Columns\TextColumn::make('inquiries_count')
                    ->label('Inquiries')
                    ->counts('inquiries')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('published_at')
                    ->label('Published')
                    ->dateTime('M j, Y')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('View')
                    ->url(fn (Listing $record): string => route('filament.admin.resources.listings.view', $record))
                    ->icon('heroicon-m-eye'),
            ]);
    }

    public static function canView(): bool
    {
        return Auth::user()?->role === 'agent';
    }
}
