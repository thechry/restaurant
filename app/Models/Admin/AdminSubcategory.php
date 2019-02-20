<?php

namespace App\Models\Admin;

/**
 * Description of Admin
 *
 * @author Comtech
 */
class AdminSubcategory extends AdminBase {
    protected $table = 'subcategories';
    
    protected $fillable = [
        'subcategory_title',
        'subcategory_desc',
        'subcategory_foto',
        'subcategory_position',
        'subcategory_active',
        'subcategory_slug',
        'subcategory_page_id',
        'subcategory_category_id'
    ];
    
    
}