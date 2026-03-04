<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Role extends Model
{
    use HasUuids;

    protected $table = 'role';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'role_name',
        'role_description',
        'role_status',
    ];

    public static function createRole($data)
    {
        return self::create([
            'role_name' => $data['role_name'],
            'role_description' => $data['role_description'] ?? null,
            'role_status' => $data['role_status'] ?? true,
        ]);
    }

    public function updateRole($data)
    {
        $this->update([
            'role_name' => $data['role_name'],
            'role_description' => $data['role_description'] ?? $this->role_description,
            'role_status' => $data['role_status'] ?? $this->role_status,
        ]);
    }

    public function toggleStatus()
    {
        $this->role_status = !$this->role_status;
        $this->save();
    }
}
