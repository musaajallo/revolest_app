<?php

namespace App\Filament\Pages;

use App\Filament\Resources\InquiryResource;
use App\Models\Inquiry;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class Notifications extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-bell';

    protected static ?string $navigationGroup = 'Communication';

    protected static ?string $navigationLabel = 'Notifications';

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'Notifications';

    protected static string $view = 'filament.pages.notifications';

    public static function canAccess(): bool
    {
        $user = Auth::user();

        if (! $user) {
            return false;
        }

        return in_array($user->role, ['super_admin', 'owner', 'agent'], true);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Inquiry::query()->latest())
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Received')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('listing.property.title')
                    ->label('Property')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'new',
                        'success' => 'read',
                        'gray' => fn (?string $state): bool => ! in_array((string) $state, ['new', 'read'], true),
                    ])
                    ->formatStateUsing(fn (?string $state): string => ucfirst((string) $state)),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'new' => 'New',
                        'read' => 'Read',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('open_enquiry')
                    ->label('Open')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (Inquiry $record): string => InquiryResource::getUrl('view', ['record' => $record]))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('mark_read')
                    ->label('Mark Read')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (Inquiry $record): bool => $record->status === 'new')
                    ->action(fn (Inquiry $record): bool => $record->update(['status' => 'read'])),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
