<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InspectionResource\Pages;
use App\Models\Inspection;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InspectionResource extends Resource
{
    protected static ?string $model = Inspection::class;

    protected static ?string $navigationGroup = 'Properties';

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    public static function getGloballySearchableAttributes(): array
    {
        return ['lease.tenant.name', 'lease.property.title', 'status'];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Lease & Property')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('lease_id')
                            ->relationship('lease', 'id')
                            ->getOptionLabelFromRecordUsing(fn ($record) => ($record->property?->title ?? 'Property')
                                . ' — ' . ($record->tenant?->name ?? 'Tenant'))
                            ->searchable(['property.title', 'tenant.name'])
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $lease = \App\Models\Lease::find($state);
                                    if ($lease) {
                                        $set('property_id', $lease->property_id);
                                    }
                                }
                            }),
                        Forms\Components\Select::make('property_id')
                            ->relationship('property', 'title')
                            ->searchable()
                            ->required(),
                    ]),

                Forms\Components\Section::make('Inspection')
                    ->columns(2)
                    ->schema([
                        Forms\Components\DateTimePicker::make('inspected_at')
                            ->required()
                            ->default(now()),
                        Forms\Components\Select::make('status')
                            ->options([
                                'pass' => 'Pass',
                                'issues_found' => 'Issues Found',
                                'fail' => 'Fail',
                                'pending_followup' => 'Pending Follow-up',
                            ])
                            ->required(),
                        Forms\Components\Select::make('inspector_user_id')
                            ->label('Inspector')
                            ->relationship('inspector', 'name')
                            ->searchable(),
                        Forms\Components\DatePicker::make('next_inspection_due_at')->label('Next Inspection Due'),
                        Forms\Components\Textarea::make('findings')->columnSpanFull()->rows(4),
                        Forms\Components\FileUpload::make('images')
                            ->multiple()
                            ->image()
                            ->disk('public')
                            ->directory('inspections')
                            ->visibility('public')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('inspected_at')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('lease.property.title')->label('Property')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('lease.tenant.name')->label('Tenant')->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (?string $state) => match ($state) {
                        'pass' => 'success',
                        'issues_found' => 'warning',
                        'fail' => 'danger',
                        'pending_followup' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('inspector.name')->label('Inspector')->toggleable(),
                Tables\Columns\TextColumn::make('next_inspection_due_at')->label('Next Due')->date()->sortable(),
            ])
            ->defaultSort('inspected_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'pass' => 'Pass',
                    'issues_found' => 'Issues Found',
                    'fail' => 'Fail',
                    'pending_followup' => 'Pending Follow-up',
                ]),
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    public static function canAccess(): bool
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        return $user && in_array($user->role, ['super_admin', 'admin', 'agent', 'owner']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInspections::route('/'),
            'create' => Pages\CreateInspection::route('/create'),
            'view' => Pages\ViewInspection::route('/{record}'),
            'edit' => Pages\EditInspection::route('/{record}/edit'),
        ];
    }
}
