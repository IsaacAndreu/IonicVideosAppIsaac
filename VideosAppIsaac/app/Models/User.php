<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Test;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasProfilePhoto, HasTeams, TwoFactorAuthenticatable, HasRoles;

    protected $fillable = ['name', 'email', 'password', 'current_team_id', 'super_admin'];

    protected $hidden = ['password', 'remember_token'];

    protected $appends = ['profile_photo_url'];

    public function getProfilePhotoUrlAttribute(): ?string
    {
        return $this->profile_photo_path
            ? asset('storage/' . $this->profile_photo_path)
            : null;
    }

    public function currentTeam()
    {
        return $this->belongsTo(Team::class, 'current_team_id');
    }

    public function testedBy(): HasMany
    {
        return $this->hasMany(Test::class, 'user_id');
    }

    public function isSuperAdmin(): bool
    {
        return $this->super_admin;
    }

    /**
     * Arxius multimèdia pujats per l'usuari.
     */
    public function multimedia(): HasMany
    {
        return $this->hasMany(\App\Models\Multimedia::class);
    }
}
