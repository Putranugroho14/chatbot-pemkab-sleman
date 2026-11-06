<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'dinas'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

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

    /**
     * Cek apakah user adalah Super Admin (IT Diskominfo).
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Cek apakah user adalah Admin Dinas tertentu.
     */
    public function isDinasAdmin(): bool
    {
        return $this->role === 'dinas_admin';
    }

    /**
     * Cek apakah user adalah Staff.
     */
    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    /**
     * Cek hak akses ke dinas tertentu.
     */
    public function canAccessDinas(?string $dinas): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        if (!$dinas) {
            return false;
        }

        return strtolower($this->dinas ?? '') === strtolower($dinas);
    }

    /**
     * Cek hak kelola agent dinas.
     */
    public function canManageAgent(string $agentDinas): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        if ($this->isDinasAdmin() && strtolower($this->dinas ?? '') === strtolower($agentDinas)) {
            return true;
        }

        return false;
    }

    /**
     * Relasi riwayat chat.
     */
    public function chatHistories(): HasMany
    {
        return $this->hasMany(ChatHistory::class);
    }
}
