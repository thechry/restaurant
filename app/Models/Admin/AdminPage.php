<?php

namespace App\Models\Admin;

/**
 * Description of AdminPage
 *
 * @author Comtech
 */

class AdminPage extends AdminBase{
    protected $table = 'pages';
    
    protected $fillable = [
        'page_name',
        'logo_name',
        'logo_img',
        'page_title'
    ];
}