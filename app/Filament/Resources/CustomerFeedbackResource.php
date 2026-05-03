<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerFeedbackResource\Pages;
use App\Models\CustomerFeedback;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CustomerFeedbackResource extends Resource
{
    protected static ?string $model = CustomerFeedback::class;

    protected static ?string $navigationGroup = 'Submissions';

    protected static ?string $navigationLabel = 'Customer Feedback';

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?int $navigationSort = 50;

    public static function getGlobalSearchResultUrl($record): string
    {
        return static::getUrl('view', ['record' => $record->getKey()]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['full_name', 'email', 'phone', 'improvement_suggestions', 'additional_comments'];
    }

    public static function form(Form $form): Form
    {
        $satisfaction = [
            'very_satisfied' => 'Very Satisfied',
            'satisfied' => 'Satisfied',
            'neutral' => 'Neutral',
            'dissatisfied' => 'Dissatisfied',
            'very_dissatisfied' => 'Very Dissatisfied',
        ];
        $quality = [
            'excellent' => 'Excellent',
            'good' => 'Good',
            'average' => 'Average',
            'poor' => 'Poor',
            'very_poor' => 'Very Poor',
        ];
        $yesNoSomewhat = ['yes' => 'Yes', 'no' => 'No', 'somewhat' => 'Somewhat'];
        $easeOfFinding = [
            'very_easy' => 'Very Easy',
            'easy' => 'Easy',
            'neutral' => 'Neutral',
            'difficult' => 'Difficult',
            'very_difficult' => 'Very Difficult',
        ];
        $recommend = ['definitely' => 'Definitely', 'maybe' => 'Maybe', 'not_likely' => 'Not Likely'];
        $score = ['1_3' => '1 – 3', '4_6' => '4 – 6', '7_10' => '7 – 10'];
        $expectations = ['yes' => 'Yes', 'no' => 'No', 'exceed' => 'Exceed'];
        $heard = [
            'word_of_mouth' => 'Word of Mouth',
            'social_media' => 'Social Media',
            'online_ad' => 'Online Advertisement',
            'friend_family' => 'Friend / Family Referral',
            'other' => 'Other',
        ];

        return $form->schema([
            Forms\Components\Section::make('Contact (optional)')->columns(3)->schema([
                Forms\Components\TextInput::make('full_name')->maxLength(255),
                Forms\Components\TextInput::make('email')->email()->maxLength(255),
                Forms\Components\TextInput::make('phone')->tel()->maxLength(50),
            ]),
            Forms\Components\Section::make('Ratings')->columns(2)->schema([
                Forms\Components\Select::make('overall_satisfaction')->options($satisfaction),
                Forms\Components\Select::make('service_quality')->options($quality),
                Forms\Components\Select::make('customer_service_experience')->options($quality),
                Forms\Components\Select::make('staff_helpful')->options($yesNoSomewhat),
                Forms\Components\Select::make('delivery_on_time')->options($yesNoSomewhat),
                Forms\Components\Select::make('ease_of_finding')->options($easeOfFinding),
                Forms\Components\Select::make('would_recommend')->options($recommend),
                Forms\Components\Select::make('accessibility_score')->options($score),
                Forms\Components\Select::make('expectations_met')->options($expectations),
                Forms\Components\Select::make('brand_score')->options($score),
            ]),
            Forms\Components\Section::make('Free Text')->schema([
                Forms\Components\Textarea::make('improvement_suggestions')->label('What could we improve?'),
                Forms\Components\Textarea::make('additional_comments'),
                Forms\Components\Textarea::make('why_chose_us'),
                Forms\Components\Textarea::make('missing_features'),
            ])->collapsed(),
            Forms\Components\Section::make('How Did You Hear?')->columns(2)->schema([
                Forms\Components\Select::make('heard_about_us')->options($heard),
                Forms\Components\TextInput::make('heard_about_us_other')->maxLength(255),
            ]),
            Forms\Components\Section::make('Triage')->columns(2)->schema([
                Forms\Components\Select::make('status')
                    ->options(array_combine(CustomerFeedback::STATUSES, CustomerFeedback::STATUSES))
                    ->required(),
                Forms\Components\Textarea::make('notes')->label('Internal notes')->columnSpanFull(),
            ]),
            Forms\Components\Section::make('Acknowledgement')->columns(2)->schema([
                Forms\Components\TextInput::make('signed_name')->maxLength(255),
                Forms\Components\DateTimePicker::make('signed_at'),
                Forms\Components\TextInput::make('ip_address')->disabled(),
                Forms\Components\DateTimePicker::make('submitted_at')->disabled(),
            ])->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('full_name')->placeholder('Anonymous')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('overall_satisfaction')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'very_satisfied' => 'success',
                        'satisfied' => 'success',
                        'neutral' => 'gray',
                        'dissatisfied' => 'warning',
                        'very_dissatisfied' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('would_recommend')->badge()->toggleable(),
                Tables\Columns\TextColumn::make('brand_score')->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'warning',
                        'reviewed' => 'info',
                        'archived' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('submitted_at')->dateTime()->sortable()->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(array_combine(CustomerFeedback::STATUSES, CustomerFeedback::STATUSES)),
                Tables\Filters\SelectFilter::make('overall_satisfaction')->options([
                    'very_satisfied' => 'Very Satisfied',
                    'satisfied' => 'Satisfied',
                    'neutral' => 'Neutral',
                    'dissatisfied' => 'Dissatisfied',
                    'very_dissatisfied' => 'Very Dissatisfied',
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
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomerFeedback::route('/'),
            'create' => Pages\CreateCustomerFeedback::route('/create'),
            'view' => Pages\ViewCustomerFeedback::route('/{record}'),
            'edit' => Pages\EditCustomerFeedback::route('/{record}/edit'),
        ];
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

    public static function canAccess(): bool
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        if (! $user) {
            return false;
        }

        return in_array($user->role, ['super_admin', 'admin', 'agent']);
    }
}
