<?php

namespace App\Filament\Resources;

use App\Enums\UserType;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    
    protected static ?string $modelLabel = 'User';
    protected static ?string $navigationLabel = 'Users';
    

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereIn('type', [
                UserType::ADMIN->value, 
                UserType::EMPLOYEE->value,
                UserType::CLIENT->value
            ])
            ->with('client');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->required()
                    ->maxLength(255)
                    ->hiddenOn('edit'),
                Forms\Components\Toggle::make('status')
                    ->required(),
                Forms\Components\Select::make('type')
                    ->options(UserType::class)
                    ->required()
                    ->reactive(),
                Forms\Components\Section::make('Admin Panel Access')
                    ->schema([
                        Forms\Components\Toggle::make('admin_access_granted')
                            ->label('Allow Admin Panel Access')
                            ->helperText('Grant this user access to the admin panel (only applies to clients)')
                            ->visible(fn (Forms\Get $get) => $get('type') === UserType::CLIENT->value),
                        Forms\Components\Placeholder::make('granted_info')
                            ->label('Permission Details')
                            ->content(function ($record) {
                                if (!$record || !$record->admin_access_granted) {
                                    return 'No admin access granted';
                                }
                                
                                $grantedBy = $record->grantedBy?->name ?? 'Unknown';
                                $grantedAt = $record->granted_at?->format('M j, Y g:i A') ?? 'Unknown';
                                
                                return "Granted by: {$grantedBy} on {$grantedAt}";
                            })
                            ->visible(fn ($record) => $record && $record->type === UserType::CLIENT && $record->admin_access_granted),
                    ])
                    ->visible(fn (Forms\Get $get) => $get('type') === UserType::CLIENT->value)
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function ($record) {
                        return $record->client ? $record->client->name : $record->name;
                    }),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function ($record) {
                        return $record->client ? $record->client->email : $record->email;
                    }),
                IconColumn::make('status')
                    ->boolean(),
                TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (UserType $state): string => match($state) {
                        UserType::CLIENT => 'Client',
                        UserType::ADMIN => 'Admin',
                        UserType::EMPLOYEE => 'Employee',
                    })
                    ->color(fn (UserType $state): string => match ($state) {
                        UserType::CLIENT => 'success',
                        UserType::ADMIN => 'danger',
                        UserType::EMPLOYEE => 'info',
                        default => 'gray',
                    }),
                IconColumn::make('admin_access_granted')
                    ->label('Admin Access')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->visible(fn ($record) => $record && $record->type === UserType::CLIENT),
                TextColumn::make('grantedBy.name')
                    ->label('Granted By')
                    ->placeholder('—')
                    ->visible(fn ($record) => $record && $record->type === UserType::CLIENT && $record->admin_access_granted),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options(UserType::class),
                Filter::make('active')
                    ->query(fn (Builder $query): Builder => $query->where('status', true))
                    ->label('Only active users'),
                TernaryFilter::make('admin_access_granted')
                    ->label('Admin Access (Clients only)')
                    ->placeholder('All clients')
                    ->trueLabel('With admin access')
                    ->falseLabel('Without admin access')
                    ->queries(
                        true: fn (Builder $query) => $query->where('type', UserType::CLIENT)->where('admin_access_granted', true),
                        false: fn (Builder $query) => $query->where('type', UserType::CLIENT)->where('admin_access_granted', false),
                        blank: fn (Builder $query) => $query,
                    ),
            ])
            ->actions([
                Action::make('toggleAdminAccess')
                    ->label(fn ($record) => $record->admin_access_granted ? 'Revoke Access' : 'Grant Access')
                    ->icon(fn ($record) => $record->admin_access_granted ? 'heroicon-o-shield-exclamation' : 'heroicon-o-shield-check')
                    ->color(fn ($record) => $record->admin_access_granted ? 'danger' : 'success')
                    ->visible(fn ($record) => $record->type === UserType::CLIENT)
                    ->action(function ($record) {
                        if ($record->admin_access_granted) {
                            $record->revokeAdminAccess();
                        } else {
                            $record->grantAdminAccess(auth()->user());
                        }
                    })
                    ->requiresConfirmation()
                    ->modalHeading(fn ($record) => $record->admin_access_granted ? 'Revoke Admin Access' : 'Grant Admin Access')
                    ->modalDescription(fn ($record) => $record->admin_access_granted 
                        ? 'Are you sure you want to revoke admin panel access for this client?'
                        : 'Are you sure you want to grant admin panel access to this client?'),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('grantAdminAccess')
                        ->label('Grant Admin Access')
                        ->icon('heroicon-o-shield-check')
                        ->color('success')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                if ($record->type === UserType::CLIENT && !$record->admin_access_granted) {
                                    $record->grantAdminAccess(auth()->user());
                                }
                            }
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Grant Admin Access')
                        ->modalDescription('Grant admin panel access to selected client users?'),
                    BulkAction::make('revokeAdminAccess')
                        ->label('Revoke Admin Access')
                        ->icon('heroicon-o-shield-exclamation')
                        ->color('danger')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                if ($record->type === UserType::CLIENT && $record->admin_access_granted) {
                                    $record->revokeAdminAccess();
                                }
                            }
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Revoke Admin Access')
                        ->modalDescription('Revoke admin panel access from selected client users?'),
                    DeleteBulkAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}