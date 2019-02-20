<?php

namespace App\Models\Admin;

/**
 * Description of Admin
 *
 * @author Comtech
 */
class AdminUser extends AdminBase {
    protected $table = 'users';
    
    protected $fillable = [
        'name',
        'username',
        'password',
        'page_id',
        'role_id'
    ];
}