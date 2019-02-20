<?php

namespace App\Models\Admin;

/**
 * Description of Admin
 *
 * @author Comtech
 */
class AdminUserRole extends AdminBase {
    protected $table = 'roles';
    
    protected $fillable = [
        'role_name',
        'role_id'
    ];
}