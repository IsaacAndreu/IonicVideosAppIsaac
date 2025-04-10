<?php

use App\Models\User;
use App\Models\Team;

if (!function_exists('crearUsuariPerDefecte')) {
    /**
     * Crea un usuari per defecte.
     *
     * @return User
     */
    function crearUsuariPerDefecte(): User
    {
        User::where('email', config('default_users.user.email'))->delete();

        $password = config('default_users.user.password');
        if (!is_string($password)) {
            throw new InvalidArgumentException('The default user password must be a string.');
        }

        $user = User::create([
            'name' => config('default_users.user.name'),
            'email' => config('default_users.user.email'),
            'password' => bcrypt($password),
        ]);

        $user->refresh();

        $team = Team::create([
            'name' => $user->name . "'s Team",
            'user_id' => $user->id,
            'personal_team' => true,
        ]);

        $user->teams()->attach($team->id, ['role' => 'owner']);
        $user->current_team_id = $team->id;
        $user->save();

        return $user;
    }
}

if (!function_exists('crearProfessorPerDefecte')) {
    /**
     * Crea un professor per defecte.
     *
     * @return User
     */
    function crearProfessorPerDefecte(): User
    {
        User::where('email', config('default_users.professor.email'))->delete();

        $password = config('default_users.professor.password');
        if (!is_string($password)) {
            throw new InvalidArgumentException('The default professor password must be a string.');
        }

        $professor = User::create([
            'name' => config('default_users.professor.name'),
            'email' => config('default_users.professor.email'),
            'password' => bcrypt($password),
        ]);

        $professor->refresh();

        $team = Team::create([
            'name' => $professor->name . "'s Team",
            'user_id' => $professor->id,
            'personal_team' => true,
        ]);

        $professor->teams()->attach($team->id, ['role' => 'owner']);
        $professor->current_team_id = $team->id;
        $professor->save();

        return $professor;
    }
}
