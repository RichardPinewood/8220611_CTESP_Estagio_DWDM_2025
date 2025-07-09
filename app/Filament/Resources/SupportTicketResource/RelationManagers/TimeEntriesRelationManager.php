<?php

namespace App\Filament\Resources\SupportTicketResource\RelationManagers;

use App\Models\TimeEntry;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Support\Facades\Auth;

class TimeEntriesRelationManager extends RelationManager
{
    protected static string $relationship = 'timeEntries';
    protected static ?string $recordTitleAttribute = 'description';
    protected static ?string $title = 'Time Entries';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('admin_id')
                    ->default(Auth::id()),
                TextInput::make('hours_spent')
                    ->label('Hours Spent')
                    ->numeric()
                    ->step(0.25)
                    ->minValue(0.25)
                    ->maxValue(24)
                    ->required()
                    ->suffix('hours'),
                DatePicker::make('work_date')
                    ->label('Work Date')
                    ->default(now())
                    ->required(),
                Textarea::make('description')
                    ->label('Work Description')
                    ->required()
                    ->rows(3)
                    ->placeholder('Describe the work performed...')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('admin.name')
                    ->label('Admin')
                    ->sortable(),
                TextColumn::make('hours_spent')
                    ->label('Hours')
                    ->formatStateUsing(fn ($state) => number_format($state, 2) . 'h')
                    ->sortable(),
                TextColumn::make('work_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),
                TextColumn::make('description')
                    ->label('Work Description')
                    ->limit(50)
                    ->wrap(),
                TextColumn::make('created_at')
                    ->label('Logged At')
                    ->dateTime()
                    ->sortable()
                    ->since(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Log Time')
                    ->icon('heroicon-o-clock')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['admin_id'] = Auth::id();
                        return $data;
                    }),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('work_date', 'desc');
    }
}