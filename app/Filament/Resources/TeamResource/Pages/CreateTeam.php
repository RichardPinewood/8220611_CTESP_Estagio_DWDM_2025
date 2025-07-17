<?php

namespace App\Filament\Resources\TeamResource\Pages;

use App\Filament\Resources\TeamResource;
use App\Enums\UserType;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateTeam extends CreateRecord
{
    protected static string $resource = TeamResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (isset($data['promote_user_id']) && $data['promote_user_id']) {
            $user = \App\Models\User::find($data['promote_user_id']);
            if ($user) {
                return [
                    'name' => $user->name,
                    'email' => $user->email,
                    'password' => $user->password, 
                    'type' => $user->type,
                    'status' => $user->status,
                    'department_id' => $data['department_id'],
                ];
            }
        }

        return [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'type' => UserType::from($data['type']),
            'status' => $data['status'] ?? true,
            'department_id' => $data['department_id'],
        ];
    }
}
