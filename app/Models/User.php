<?php

namespace App\Models;

use App\Models\HR\PersonAffiliation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'person_id',
        'email',
        'password',
        'person_affiliations_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'name',
    ];

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

    public function person()
    {
        return $this->belongsTo(Person::class, 'person_id');
    }

    public function personAffiliation()
    {
        return $this->belongsTo(PersonAffiliation::class, 'person_affiliations_id');
    }

    public function getNameAttribute(): string
    {
        if ($this->relationLoaded('person') && $this->person) {
            return trim("{$this->person->first_name} {$this->person->last_name}");
        }

        if ($this->person_id) {
            $person = Person::find($this->person_id);
            if ($person) {
                return trim("{$person->first_name} {$person->last_name}");
            }
        }

        return explode('@', $this->email)[0] ?? 'User';
    }
}

