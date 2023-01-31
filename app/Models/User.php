<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    // public function scopeCountReferals($query)
    // {
    //    // return User::withCount('referrals')->get();
    //     return $query->withCount('referrals');
    // }



    public function orders()
    {
        return $this->hasMany(Order::class, 'purchaser_id');
    }

    // public function usercategory()
    // {
    //     return $this->hasOne(UserCategory::class, 'user_id');
    // }

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }


    public function referrals()
    {

        return $this->hasMany(User::class, 'referred_by');
    }

    public function categories()
    {

        return $this->belongsToMany(Category::class, 'user_category');
    }

    public function referredDistributors()
    {
        return $this->referrals()->whereHas('categories', function ($query) {
            return  $query->where('name', 'Distributor');
        });
    }
}
