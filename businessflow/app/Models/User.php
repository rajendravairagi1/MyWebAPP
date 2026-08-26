<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'avatar'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function businesses(): BelongsToMany
    {
        return $this->belongsToMany(Business::class)
            ->using(BusinessUser::class)
            ->withPivot(['role', 'permissions', 'status'])
            ->withTimestamps();
    }

    /**
     * The Company this user owns (Company Owner tier), if any. A user
     * can own at most one Company today — see App\Models\Company.
     */
    public function ownedCompany(): HasOne
    {
        return $this->hasOne(Company::class, 'owner_user_id');
    }

    /**
     * Branches this user is the assigned manager of (Branch Manager
     * tier) — see App\Models\Branch.
     */
    public function managedBranches(): HasMany
    {
        return $this->hasMany(Branch::class, 'manager_user_id');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
