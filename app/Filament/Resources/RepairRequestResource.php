<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RepairRequestResource\Pages;
use App\Models\RepairRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RepairRequestResource extends Resource
{
    protected static ?string $model = RepairRequest::class;

    protected static ?string $navigationGroup = 'Properties';

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    public static function getGlobalSearchResultUrl($record): string
    {
        return static::getUrl('view', ['record' => $record->getKey()]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['description', 'first_name', 'last_name', 'email', 'phone', 'property_address'];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Tenant Contact')->columns(2)->schema([
                Forms\Components\Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->searchable()
                    ->helperText('Optional — link to a known tenant'),
                Forms\Components\Select::make('property_id')
                    ->relationship('property', 'title')
                    ->searchable()
                    ->helperText('Optional — link to a known property'),
                Forms\Components\TextInput::make('first_name')->maxLength(100),
                Forms\Components\TextInput::make('last_name')->maxLength(100),
                Forms\Components\TextInput::make('email')->email()->maxLength(255),
                Forms\Components\TextInput::make('phone')->tel()->maxLength(50),
                Forms\Components\TextInput::make('property_address')->maxLength(255),
                Forms\Components\TextInput::make('apartment_number')->label('Apartment / Compound Number')->maxLength(100),
            ]),
            Forms\Components\Section::make('Maintenance Detail')->columns(2)->schema([
                Forms\Components\Textarea::make('description')->required()->columnSpanFull(),
                Forms\Components\Select::make('priority')
                    ->options(array_combine(RepairRequest::PRIORITIES, array_map('ucfirst', RepairRequest::PRIORITIES))),
                Forms\Components\TextInput::make('category')->maxLength(100),
                Forms\Components\Select::make('preferred_visit')
                    ->options([
                        'home' => 'Yes, I prefer to be home',
                        'anytime' => 'No, come anytime',
                        'call_to_confirm' => 'Call to confirm',
                        'fix_appointment' => 'Fix appointment',
                    ]),
                Forms\Components\Toggle::make('has_pets'),
                Forms\Components\Textarea::make('pet_notes')->columnSpanFull(),
                Forms\Components\Toggle::make('permission_to_enter')
                    ->label('Permission to enter granted')
                    ->columnSpanFull(),
            ]),
            Forms\Components\Section::make('Triage')->columns(2)->schema([
                Forms\Components\Select::make('status')
                    ->options(array_combine(RepairRequest::STATUSES, array_map(fn ($s) => str_replace('_', ' ', ucfirst($s)), RepairRequest::STATUSES)))
                    ->required(),
                Forms\Components\DateTimePicker::make('submitted_at'),
                Forms\Components\DateTimePicker::make('resolved_at'),
            ]),
            Forms\Components\Section::make('Completion')->columns(2)->schema([
                Forms\Components\DateTimePicker::make('completed_at'),
                Forms\Components\TextInput::make('completed_by_name')->label('Completed by')->maxLength(255),
                Forms\Components\Textarea::make('completion_notes')->columnSpanFull(),
            ])->collapsed(),
            Forms\Components\Section::make('Acknowledgement')->columns(2)->schema([
                Forms\Components\TextInput::make('tenant_signature_name')->maxLength(255),
                Forms\Components\DateTimePicker::make('signed_at'),
                Forms\Components\TextInput::make('ip_address')->disabled(),
                Forms\Components\TextInput::make('user_agent')->disabled(),
            ])->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Tenant')
                    ->getStateUsing(fn (RepairRequest $r) => $r->full_name)
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    }),
                Tables\Columns\TextColumn::make('property_address')
                    ->label('Property')
                    ->getStateUsing(fn (RepairRequest $r) => $r->property?->title ?? $r->property_address)
                    ->searchable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('description')->limit(50),
                Tables\Columns\TextColumn::make('priority')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'emergency' => 'danger',
                        'urgent' => 'warning',
                        'immediate' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'warning',
                        'in_progress' => 'info',
                        'awaiting_parts' => 'gray',
                        'completed' => 'success',
                        'cancelled' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('has_pets')->boolean()->toggleable(),
                Tables\Columns\TextColumn::make('submitted_at')->dateTime()->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('completed_at')->dateTime()->sortable()->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(array_combine(RepairRequest::STATUSES, RepairRequest::STATUSES)),
                Tables\Filters\SelectFilter::make('priority')
                    ->options(array_combine(RepairRequest::PRIORITIES, RepairRequest::PRIORITIES)),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->color('success'),
                Tables\Actions\EditAction::make()->color('warning'),
                Tables\Actions\DeleteAction::make()->color('danger'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRepairRequests::route('/'),
            'create' => Pages\CreateRepairRequest::route('/create'),
            'view' => Pages\ViewRepairRequest::route('/{record}'),
            'edit' => Pages\EditRepairRequest::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('status', 'new')->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
