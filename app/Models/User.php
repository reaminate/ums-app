<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserType;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'type', 'email', 'password', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;
    public function lecturer(): HasOne 
    {
        return $this->hasOne(Lecturer::class, 'user_id');
    }
    public function student(): HasOne 
    {
        return $this->hasOne(Student::class, 'user_id');
    }
    public function isAdmin(): bool
    {
        if($this->type != UserType::ADMIN->value){
            return false;
        }
        return true;
    }
    public function isLecturer(): bool
    {
        if($this->type == UserType::STUDENT->value){
            return false;
        }
        return true;
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
