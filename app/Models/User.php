<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory,Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_role');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function hasPermission($permission)
    {
        // Nếu dùng bảng permissions, bạn có thể kiểm tra thực tế:
        // return $this->permissions()->where('name', $permission)->exists();

        // Tạm thời: giả định role có mảng quyền
        $rolePermissions = [
            'admin' => ['manage-flows', 'view-reports', 'assign-managers'],
            'manager' => ['view-reports'],
        ];

        return in_array($permission, $rolePermissions[$this->role] ?? []);
    }

    public function permissions()
    {
        return $this->roles()->with('permissions')->get()->pluck('permissions')->flatten()->unique('id');
    }

    public function hasRole($slug)
    {
        return $this->roles()->where('slug', $slug)->exists();
    }
}
