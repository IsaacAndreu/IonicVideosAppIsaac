<?php

namespace App\Actions\Jetstream;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Laravel\Jetstream\Contracts\DeletesUsers;

class DeleteUser implements DeletesUsers
{
    /**
     * Delete the given user.
     */
    public function delete(User $user): void
    {
        DB::transaction(function () use ($user) {
            $user->teams()->detach();

            $user->ownedTeams->each(function ($team) {
                /** @var \Illuminate\Database\Eloquent\Model $team */
                $team->forceDelete();
            });

            $user->deleteProfilePhoto();
            $user->tokens->each->delete();

            $user->delete();
        });
    }
}
