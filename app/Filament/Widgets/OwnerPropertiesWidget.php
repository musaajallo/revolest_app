<?php

namespace App\Filament\Widgets;

use App\Models\Property;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class OwnerPropertiesWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $user = Auth::user();
        $owner = $user?->owner ?? \App\Models\Owner::where('user_id', $user?->id)->first();

        return $table
            ->heading('My Properties')
            ->query(
                Property::query()
                    ->where('owner_id', $owner?->id)
                    ->withCount('leases')
                    ->latest()
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Property')
                    ->searchable()
                    ->sortable()
                    ->limit(40),

                Tables\Columns\TextColumn::make('address')
                    ->label('Address')
                    ->limit(30)
                    ->searchable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'for_rent',
                        'success' => 'for_sale',
                        'primary' => 'rented',
                        'danger' => 'sold',
                    ]),

                Tables\Columns\TextColumn::make('price')
                    ->label('Price')
                    ->money('GMD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('leases_count')
                    ->label('Leases')
                    ->badge()
                    ->color('info'),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('View')
                    ->url(fn (Property $record): string => route('filament.admin.resources.properties.view', $record))
                    ->icon('heroicon-m-eye'),
            ]);
    }

    public static function canView(): bool
    {
        return Auth::user()?->role === 'owner';
    }
}
