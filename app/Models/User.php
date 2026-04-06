<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'identity_number',
        'role',
        'email',
        'phone',
        'is_active',
    ];

    /**
     * Relationship: User has many loans
     */
    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    /**
     * Helper: Check if user is a teacher
     */
    public function isTeacher(): bool
    {
        return $this->role === 'teacher';
    }
}
