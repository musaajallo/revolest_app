<?php

namespace App\Filament\RelationManagers;

use App\Models\LeadActivity;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class LeadActivitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'activities';

    protected static ?string $title = 'Follow-ups & Notes';

    protected static ?string $recordTitleAttribute = 'kind';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('kind')
                ->options(LeadActivity::KINDS)
                ->default('note')
                ->required(),
            Forms\Components\Textarea::make('body')
                ->rows(4)
                ->required()
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->dateTime('Y-m-d H:i')->sortable(),
                Tables\Columns\TextColumn::make('kind')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => $state ? (LeadActivity::KINDS[$state] ?? $state) : null)
                    ->color(fn (?string $state) => match ($state) {
                        'status_change' => 'warning',
                        'meeting', 'viewing' => 'success',
                        'contact_attempt' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('user.name')->label('By')->toggleable(),
                Tables\Columns\TextColumn::make('body')->wrap(),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['user_id'] = Auth::id();
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
