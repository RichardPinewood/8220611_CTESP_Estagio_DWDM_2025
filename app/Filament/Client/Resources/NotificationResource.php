<?php

namespace App\Filament\Client\Resources;

use App\Filament\Client\Resources\NotificationResource\Pages;
use App\Models\Notification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class NotificationResource extends Resource
{
    protected static ?string $model = Notification::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell';
    
    protected static ?string $navigationLabel = 'Notifications';
    
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->sortable(false)
                    ->searchable(false),
                
                Tables\Columns\TextColumn::make('message')
                    ->label('Message')
                    ->sortable(false)
                    ->searchable(false),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(false)
                    ->searchable(false),
                
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('filter')
                    ->options([
                        'all' => 'All',
                        'new' => 'New',
                        'latest' => 'Latest',
                    ])
                    ->default('all')
                    ->query(function ($query, $data) {
                        if ($data['value'] === 'new') {
                            return $query->whereNull('read_at');
                        }
                        if ($data['value'] === 'latest') {
                            return $query->where('created_at', '>=', now()->subDays(7));
                        }
                        return $query;
                    }),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make()
                    ->label('Delete')
                    ->requiresConfirmation(false),
            ])
            ->bulkActions([
                //
            ])
            ->emptyStateHeading('No notifications yet')
            ->emptyStateDescription('When you receive notifications, they will appear here.')
            ->emptyStateIcon('heroicon-o-bell')
            ->recordUrl(null)
            ->filtersTriggerAction(null)
            ->paginated(false);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNotifications::route('/'),
            'view' => Pages\ViewNotification::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('client_id', auth()->id())->unread()->count();
    }
}