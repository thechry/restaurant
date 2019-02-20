<?php
namespace app\Bootstrap;

use App\Controllers\HomeController;

use App\Middleware\AuthMiddleware;

use App\Middleware\GuestMiddleware;

use App\Controllers\Admin\AdminHomeController;

use App\Controllers\Admin\Page\AdminPageController;

use App\Controllers\Admin\Category\AdminCategoryController;

use App\Controllers\Admin\Subcategory\AdminSubcategoryController;

use App\Controllers\Admin\Test\TabsTestController;

use \App\Controllers\User\UserCategoryController;

use App\Controllers\Admin\Product\AdminProductController;

use App\Middleware\PathMiddleware;

// Admin
$app->group('/admin', function() {
    $this->group('/dashboard', function() {
        // Home
        $this->get('', AdminHomeController::class . ':adminHome')->setName('admin_home');
        // End Home
        
        // Page Settings
        $this->get('/page_settings', AdminPageController::class . ':createPage')->setName('page_settings_form');
        $this->post('/page_settings', AdminPageController::class . ':postPage');
        // End Page Settings
        
        // Category
        $this->get('/category/insert', AdminCategoryController::class . ':insertCategory')->setName('insert_category_form');
        $this->post('/category/insert', AdminCategoryController::class . ':postCategory');
        $this->get('/category/list', AdminCategoryController::class . ':listCategory')->setName('show_categories');
        $this->post('/category/edit', AdminCategoryController::class . ':editCategory')->setName('edit_category');
        $this->post('/category/edit/post', AdminCategoryController::class . ':postEditedCategory')->setName('post_edited_category');
        // End Category
        
        // Subcategory        
        $this->get('/subcategory/insert', AdminSubcategoryController::class . ':insertSubcategory')->setName('insert_subcategory_form');
        $this->post('/subcategory/insert', AdminSubcategoryController::class . ':postSubcategory');
        $this->get('/subcategory/list', AdminSubcategoryController::class . ':listSubcategory')->setName('show_subcategories');
        $this->post('/subcategory/edit', AdminSubcategoryController::class . ':editSubcategory')->setName('edit_subcategory');
        $this->post('/subcategory/edit/post', AdminSubcategoryController::class . ':postEditedSubcategory')->setName('post_edited_subcategory');
        // End Subcategory        
        
        // Product
        $this->get('/product/insert', AdminProductController::class . ':insertProduct')->setname('insert_product_form');
        $this->post('/product/insert', AdminProductController::class . ':postProduct');
        $this->get('/product/list', AdminProductController::class . ':listProduct')->setName('show_products');
        $this->post('/product/edit', AdminProductController::class .':editProduct')->setName('edit_product');
        $this->post('/product/edit/post', AdminProductController::class . ':postEditedProduct')->setName('post_edited_product');
        // End Product
    });

    // Signup
    $this->get('/signup', AdminHomeController::class . ':getSignUp')->setName('admin_signup');
    $this->post('/signup', AdminHomeController::class . ':postSignUp');
    // End Signup
    
    // Signout
    $this->get('/signout', AdminHomeController::class . ':getSignOut')->setName('admin_signout');
    // End Signout
})->add(new AuthMiddleware($container));
// End Admin

/***************************/

// Signin
$app->group('', function() {
    $this->get('/admin/signin', AdminHomeController::class . ':getSignIn')->setName('admin_signin');
    $this->post('/admin/signin', AdminHomeController::class . ':postSignIn');
})->add(new GuestMiddleware($container));
// End Signin

/***************************/

// User
$app->group('/index/{locale}', function() use ($app) {
    $this->get('', HomeController::class . ':index');
    $app->get('/category/{slug}', HomeController::class . ':showCategory')->setName('user_category');
    $app->get('/subcategory/{slug}', HomeController::class . ':showSubcategory')->setName('user_subcategory');
    $app->get('/product/{slug}', HomeController::class . ':showProduct')->setName('user_product');
});

//$app->get('/', HomeController::class . ':index')->setName('index');
//$app->get('/category/{slug}', HomeController::class . ':showCategory')->setName('user_category');
//$app->get('/subcategory/{slug}', HomeController::class . ':showSubcategory')->setName('user_subcategory');
//$app->get('/product/{slug}', HomeController::class . ':showProduct')->setName('user_product');
// End User

// Tests
$app->get('/admin/test/tabs', TabsTestController::class . ':index');
// End Tests