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
#[Hidden(['password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes'])]
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
     * Whether this user is entitled to create a Company — i.e. the
     * platform admin has upgraded one of their owned businesses to the
     * 'company' plan tier. Gates /company/create; self-serve Company
     * creation isn't otherwise reachable without this.
     */
    public function hasCompanyPlan(): bool
    {
        return $this->businesses()->wherePivot('role', 'owner')->where('plan', 'company')->exists();
    }

    public function hasEnabledTwoFactor(): bool
    {
        return $this->two_factor_secret !== null && $this->two_factor_confirmed_at !== null;
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
            'two_factor_secret' => 'encrypted',
            'two_factor_recovery_codes' => 'encrypted:array',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }
}
