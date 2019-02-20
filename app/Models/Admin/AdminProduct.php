<?php

namespace App\Models\Admin;

use App\Models\Admin\AdminBase;

/**
 * Description of AdminProduct
 *
 * @author Comtech
 */
class AdminProduct extends AdminBase {
    protected $table ='products';
    
    protected $fillable = [
        'product_title',
        'product_desc',
        'product_featured',
        'product_active',
        'product_order',
        'product_slug',
        'product_price',
        'product_time_todo',
        'product_foro',
    ];
}
