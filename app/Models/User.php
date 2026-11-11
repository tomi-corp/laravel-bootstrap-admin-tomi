<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, SoftDeletes;

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    public function kyc()
    {
        return $this->hasOne(kyc_user::class, 'user_id', 'id')->latestOfMany();
    }

    public function privy()
    {
        return $this->hasOne(PrivyDigitalIdSession::class, 'user_id', 'id')->latestOfMany();
    }

    public function hasLegacyPrivyVerification(): bool
    {
        return in_array($this->verified_privy, ['y', '1', 1, true], true) && ! empty($this->id_privy);
    }

    public function verifiedDigitalIdSession(): ?PrivyDigitalIdSession
    {
        return PrivyDigitalIdSession::where('user_id', $this->id)
            ->where('status', 'verified')
            ->whereNotNull('privy_id')
            ->latest('verified_at')
            ->latest('id')
            ->first();
    }

    public function hasVerifiedPrivy(): bool
    {
        if ($this->hasLegacyPrivyVerification()) {
            return true;
        }

        return $this->verifiedDigitalIdSession() !== null;
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
