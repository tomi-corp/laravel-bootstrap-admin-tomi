<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoleMenuPermission extends Model
{
    use SoftDeletes;

    public const PERMISSIONS = [
        'can_view',
        'can_create',
        'can_update',
        'can_delete',
        'can_restore',
        'can_import',
        'can_export',
        'can_submit',
        'can_verify',
        'can_approve',
        'can_reject',
        'can_assign',
        'can_download',
        'can_print',
        'can_audit',
        'can_manage',
    ];

    protected $fillable = [
        'role_id',
        'menu_id',
        ...self::PERMISSIONS,
    ];

    protected $casts = [
        'role_id' => 'integer',
        'menu_id' => 'integer',
        'can_view' => 'boolean',
        'can_create' => 'boolean',
        'can_update' => 'boolean',
        'can_delete' => 'boolean',
        'can_restore' => 'boolean',
        'can_import' => 'boolean',
        'can_export' => 'boolean',
        'can_submit' => 'boolean',
        'can_verify' => 'boolean',
        'can_approve' => 'boolean',
        'can_reject' => 'boolean',
        'can_assign' => 'boolean',
        'can_download' => 'boolean',
        'can_print' => 'boolean',
        'can_audit' => 'boolean',
        'can_manage' => 'boolean',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }
}
