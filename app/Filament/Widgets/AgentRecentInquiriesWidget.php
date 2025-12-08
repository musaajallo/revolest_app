<?php

namespace App\Filament\Widgets;

use App\Models\Inquiry;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class AgentRecentInquiriesWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $user = Auth::user();
        $agent = $user?->agent ?? \App\Models\Agent::where('user_id', $user?->id)->first();

        return $table
            ->heading('Recent Inquiries')
            ->query(
                Inquiry::query()
                    ->whereHas('listing', function ($query) use ($agent) {
                        $query->where('agent_id', $agent?->id);
                    })
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('listing.property.title')
                    ->label('Property')
                    ->limit(30)
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'contacted',
                        'danger' => 'closed',
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Received')
                    ->dateTime('M j, Y g:i A')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('View')
                    ->url(fn (Inquiry $record): string => route('filament.admin.resources.inquiries.view', $record))
                    ->icon('heroicon-m-eye'),
            ]);
    }

    public static function canView(): bool
    {
        return Auth::user()?->role === 'agent';
    }
}
