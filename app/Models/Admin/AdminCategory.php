<?php

namespace App\Models\Admin;

/**
 * Description of Admin
 *
 * @author Comtech
 */
class AdminCategory extends AdminBase {
    protected $table = 'categories';
    
    protected $fillable = [
        'category_title',
        'category_desc',
        'category_active',
        'category_position',
        'category_foto',
        'category_slug',
        'page_id'
    ];
}