<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;

use App\Models\Admin\AdminSubcategory;

use App\Controllers\HomeController;

use Interop\Container\ContainerInterface;

/**
 * Description of UserCategoryController
 *
 * @author Comtech
 */
class UserCategoryController extends BaseController 
{
    public function showCategory($request, $response, $args)
    {
        $cat_id = $args['categoryId'];
        $subcats = BaseController::getColumn('subcategories', 'subcategory_category_id', '=', $cat_id);
        $home = new HomeController($c);
        
        $logoImg = $home->getLogoImg();
        $pageTitle = $home->getPageTitle();
        $pageLogoName = $home->getPageLogoName();
        
        if(empty($logoImg)) {
            return $this->c->view->render($response, '/comtechbs/user_data.twig', [
                'page_title' => $pageTitle,
                'logo_name' => $pageLogoName,
                'path' => '',
                'subcats' => $subcats
            ]);
        }
        return $this->c->view->render($response, '/comtechbs/user_data.twig', [
            'page_title' => $pageTitle,
            'logo_name' => $pageLogoName,
            'logo_img' => $this->getLogoImg(),
            'path' => '/restaurant-slim-v1/public/img/logo/',
            //'path' => '/restaurant-v1_1/public/img/logo/', // linux
            'subcats' => $subcats
        ]);
        
       
        var_dump($subcats[1]->subcategory_title);
        die();
    }
}
