<?php

namespace App\Filament\Pages;

use App\Models\ActivityLog;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Collection;

class ActivityLogs extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'System Management';
    protected static ?string $navigationLabel = 'Activity Logs';
    protected static ?int $navigationSort = 4;
    protected static string $view = 'filament.pages.activity-logs';

    public string $activeTab = 'user';

    public function table(Table $table): Table
    {
        return $table
            ->query(ActivityLog::query()->latest())
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date & Time')
                    ->dateTime('M d, Y H:i:s')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user_name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('action')
                    ->label('Action')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        'logged_in' => 'success',
                        'logged_out' => 'gray',
                        'viewed' => 'info',
                        default => 'primary',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst(str_replace('_', ' ', $state))),
                Tables\Columns\TextColumn::make('model_type')
                    ->label('Resource')
                    ->formatStateUsing(fn (?string $state): string => $state ? class_basename($state) : '-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('model_label')
                    ->label('Item')
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->model_label),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('action')
                    ->options([
                        'created' => 'Created',
                        'updated' => 'Updated',
                        'deleted' => 'Deleted',
                        'logged_in' => 'Logged In',
                        'logged_out' => 'Logged Out',
                    ]),
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('User')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->modalContent(fn (ActivityLog $record) => view('filament.pages.partials.activity-log-detail', ['record' => $record])),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s');
    }

    public function setActiveTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function getApplicationLogs(): Collection
    {
        $logPath = storage_path('logs/laravel.log');

        if (!File::exists($logPath)) {
            return collect();
        }

        $content = File::get($logPath);
        $pattern = '/\[(\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2})\]\s(\w+)\.(\w+):\s(.+?)(?=\[\d{4}-\d{2}-\d{2}|$)/s';

        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

        return collect($matches)->map(function ($match) {
            return [
                'date' => $match[1],
                'environment' => $match[2],
                'level' => strtoupper($match[3]),
                'message' => trim(substr($match[4], 0, 500)),
            ];
        })->reverse()->take(100)->values();
    }
}
