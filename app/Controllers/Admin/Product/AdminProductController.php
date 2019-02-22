<?php

namespace App\Controllers\Admin\Product;

use \App\Controllers\Admin\AdminBaseController;

use Slim\Http\UploadedFile;

use Respect\Validation\Validator as v;

use App\Models\Admin\AdminProduct;

use \App\Models\Admin\AdminSubcategory;

use \App\Models\Admin\AdminCategory;

/**
 * Description of AdminProductController
 *
 * @author Comtech
 */
class AdminProductController extends AdminBaseController
{
    public function insertProduct($request, $response)
    {
        // Update page settings block
        $page_data = AdminBaseController::initPage($request, $response);
        $pageNames = unserialize($page_data->page_name);
        $pageTitles = unserialize($page_data->page_title);
        $pageLogoNames = unserialize($page_data->logo_name);
              
        $categories = AdminCategory::all();
        $cat_length = count($categories);
        
        for($i = 0; $i < $cat_length; $i++) {
            $categoryTitlesUnserialized[$i] = unserialize($categories[$i]->category_title);
            $categoryDescsUnserialized[$i] = unserialize($categories[$i]->category_desc);
        }

        $subcategories = AdminSubcategory::all();
        $subcat_length = count($subcategories);
        
        for($i = 0; $i < $subcat_length; $i++) {
            $subcategoryTitlesUnserialized[$i] = unserialize($subcategories[$i]->subcategory_title);
            $subcategoryDescsUnserialized[$i] = unserialize($subcategories[$i]->subcategory_desc);
        }
        
//         $find = AdminSubcategory::select('id', 'subcategory_title')->where('subcategory_category_id', '=', 14)->get();
// var_dump($find[1]->subcategory_title);
 //die();

        if(empty($page_data->logo_img)) {
            return $this->c->view->render($response, '/admin/product/insert_product.twig', [
                'logo' => $pageLogoNames,
                'logo_img' => '',
                'title' => $pageTitles, 
                'page' => $pageNames,
                'path' => '',
                'categories' => $categories,
                'cat_titles' => $categoryTitlesUnserialized,
                'cat_descs' => $categoryDescsUnserialized,
                'cat_size' => $cat_length,
                'subcategories' => $subcategories,
                'subcat_titles' => $subcategoryTitlesUnserialized,
                'subcat_descs' => $subcategoryDescsUnserialized,
                'subcat_size' => $subcat_length,
                'page_id' => $page_data->id,
            ]);
        }
       
       return $this->c->view->render($response, '/admin/product/insert_product.twig', [
            'logo' => $pageLogoNames,
            'logo_img' => $page_data->logo_img,
            'title' => $pageTitles, 
            'page' => $pageNames,
            'path' => '/restaurant-slim-v1/public/img/logo/',
            //'path' => '/restaurant-v1_1/public/img/logo/'  // linux
            'categories' => $categories,
            'cat_titles' => $categoryTitlesUnserialized,
            'cat_descs' => $categoryDescsUnserialized,
            'cat_size' => $cat_length,
            'subcategories' =>$subcategories,
            'subcat_titles' => $subcategoryTitlesUnserialized,
            'subcat_descs' => $subcategoryDescsUnserialized,
            'subcat_size' => $subcat_length,
            'page_id' => $page_data->id,
       ]);
       // End Update page settings block
    }
    
    // Ajax html select option from other html select
    public function subcatData($request, $response, $args) {
        if($request->isXhr()) { // XMLHttpRequest (AJax)
            $catid = (int) $request->getParam('get_option'); // return string
            $find = AdminSubcategory::select('id', 'subcategory_title')->where('subcategory_category_id', $catid)->get();
            $len = count($find);
            for($i = 0; $i < $len; $i++) {
                $option[$i] = unserialize($find[$i]->subcategory_title);
                echo '<option value="' . $find[$i]->id . '">' . $option[$i][0][0] . '</option>';
            }
            exit;
        }
    }
    
    public function postProduct($request, $response)
    {
        $validation = $this->c->validator->validate($request, [
            'productname_gr' => v::notEmpty(),
            'productdescription_gr' => v::notEmpty(),
            'productname_en' => v::notEmpty(),
            'productdescription_en' => v::notEmpty(),
            'productprice' => v::numeric(),
            'productorder' => v::digit(),
            'producttimetodo' => v::numeric(),
        ]);

        if($validation->failed()) {
            //echo "validation error";
            return $response->withRedirect($this->c->router->pathFor('insert_product_form'));
        }
          
        $page = AdminBaseController::getColumn('pages', 'id', '=', 1);
        
        $data = $request->getParams();
        
        $productTitlesSerialized = serialize([[$data['productname_gr']], [$data['productname_en']]]);
        $productDescssSerialized = serialize([[$data['productdescription_gr']], [$data['productdescription_en']]]);

        $dir = $this->c->get('product_upload_directory');

        $files = $request->getUploadedFiles();
        
        $file = $files['productimg'];

        if($file->getError() === UPLOAD_ERR_OK) {
            $uploadFileName = $file->getClientFilename();
            $file->moveTo("$dir/$uploadFileName");
        }
        
        if(is_null($data['productactive'])) {
            $active = 0;
        } else {
            $active = 1;
        }        

        if(is_null($data['productfeatured'])) {
            $featured = 0;
        } else {
            $featured = 1;
        }
        
        $product_slug = str_slug($data['productname_gr']);
        
        $data_inserted = AdminProduct::insertGetId([
            'product_title' => $productTitlesSerialized,
            'product_desc' => $productDescssSerialized,
            'product_active' => $active,
            'product_featured' => $featured,
            'product_slug' => $product_slug,
            'product_order' => $data['productorder'],
            'product_price' => $data['productprice'],
            'product_time_todo' => $data['producttimetodo'],
            'product_foto' => $uploadFileName,
            //'product_page_id' => $page[0]->id,
            'product_category_id' => $data['selectcategory'],
            'product_subcategory_id' => $data['selectsubcategory']
        ]);
        
        return $response->withRedirect($this->c->router->pathFor('show_products'));
    }
    
    public function listProduct($request, $response)
    {
        $page_data = AdminBaseController::initPage($request, $response);
        $pageNames = unserialize($page_data->page_name);
        $pageTitles = unserialize($page_data->page_title);
        $pageLogoNames = unserialize($page_data->logo_name);
        
        $product_exists = 0;
        $product_data = AdminProduct::all();
        $prod_length = count($product_data);
        
        for($i = 0; $i < $prod_length; $i++) {
            $productTitlesUnserialized[$i] = unserialize($product_data[$i]->product_title);
            $productDescsUnserialized[$i] = unserialize($product_data[$i]->product_desc);
            $catIds[$i] = $product_data[$i]->product_category_id;
            $subcatIds[$i] = $product_data[$i]->product_subcategory_id;
            $catNames[$i] = AdminBaseController::getColumn('categories', 'id', '=', $catIds[$i]);
            $catNamesUnserialized[$i] = unserialize($catNames[$i][0]->category_title);
            $subcatNames[$i] = AdminBaseController::getColumn('subcategories', 'id', '=', $subcatIds[$i]);
            $subcatNamesUnserialized[$i] = unserialize($subcatNames[$i][0]->subcategory_title);
        }

//        var_dump($subcatNamesUnserialized);
//        die();
        
        $product_cat_id = $product_data[0]->product_category_id;
        $product_subcat_id = $product_data[0]->product_subcategory_id;

        $cat_data = AdminBaseController::getColumn('categories', 'id', '=', $product_cat_id);
        $catTitleUnserialized = unserialize($cat_data[0]->category_title);

        $subcat_data = AdminBaseController::getColumn('subcategories', 'id', '=', $product_subcat_id);
        $subcatTitleUnserialized = unserialize($subcat_data[0]->subcategory_title);
       
        if(!is_null($product_data[0])) {
            $product_exists = 1;
        }

        if(empty($page_data->logo_img)) {
            return $this->c->view->render($response, '/admin/product/list_product.twig', [
                'products' => $product_data,
                'product_title' => $productTitlesUnserialized,
                'product_desc' => $productDescsUnserialized,
                'product_order' => $product_data[0]->product_order,
                'product_price' => $product_data[0]->product_price,
                'product_foto' => $product_data[0]->product_foto,
                'product_active' => $product_data[0]->product_active,
                'product_featured' => $product_data[0]->product_featured,
                'product_time_todo' => $product_data[0]->product_time_todo,
                'product_cat_id' => $product_data[0]->product_category_id,
                'product_subcat_id' => $product_data[0]->product_subcategory_id,
                'product_slug' => $product_data[0]->product_slug,
                //'cat_name' => $catTitleUnserialized,
                'cat_name' => $catNamesUnserialized,
                'cat_id' => $cat_data[0]->id,
                //'subcat_name' => $subcatTitleUnserialized,
                'subcat_name' => $subcatNamesUnserialized,
                'subcat_id' => $subcat_data[0]->id,
                'exists' => $product_exists,
                'product_path' => '/restaurant-slim-v1/public/img/product/',
                //'product_path' => '/restaurant-v1_1/public/img/product/', // linux
                'logo' => $pageLogoNames,
                'logo_img' => '',
                'title' => $pageTitles, 
                'page' => $pageNames,
                'path' => ''
            ]);
        }
       
        return $this->c->view->render($response, '/admin/product/list_product.twig', [
            'products' => $product_data,
            'product_title' => $productTitlesUnserialized,
            'product_desc' => $productDescsUnserialized,
            'product_order' => $product_data[0]->product_order,
            'product_price' => $product_data[0]->product_price,
            'product_time_todo' => $product_data[0]->product_time_todo,
            'product_foto' => $product_data[0]->product_foto,
            'product_active' => $product_data[0]->product_active,
            'product_featured' => $product_data[0]->product_featured,
            'product_cat_id' => $product_data[0]->product_category_id,
            'product_subcat_id' => $product_data[0]->product_subcategory_id,
            'product_slug' => $product_data[0]->product_slug,
            //'cat_name' => $catTitleUnserialized,
            'cat_name' => $catNamesUnserialized,
            'cat_id' => $cat_data[0]->id,
            //'subcat_name' => $subcatTitleUnserialized,
            'subcat_name' => $subcatNamesUnserialized,
            'subcat_id' => $subcat_data[0]->id,
            'exists' => $product_exists,
            'product_path' => '/restaurant-slim-v1/public/img/product/',
            //'product_path' => '/restaurant-v1_1/public/img/product/', // linux
            'logo' => $pageLogoNames,
            'logo_img' => $page_data->logo_img,
            'title' => $pageTitles, 
            'page' => $pageNames,
            'path' => '/restaurant-slim-v1/public/img/logo/',
            //'path' => '/restaurant-v1_1/public/img/logo/', // linux
       ]);
    }    
    
    public function editProduct($request, $response) 
    {
        $page_data = AdminBaseController::initPage($request, $response);
        $pageNames = unserialize($page_data->page_name);
        $pageTitles = unserialize($page_data->page_title);
        $pageLogoNames = unserialize($page_data->logo_name);

        $product_id = $request->getParam('aProduct');
        $product_data = AdminBaseController::getColumn('products', 'id', '=', $product_id);
        $productTitlesUnserialized = unserialize($product_data[0]->product_title);
        $productDescsUnserialized = unserialize($product_data[0]->product_desc);


        $prod_cat_data = AdminBaseController::getColumn('categories', 'id','=', $product_data[0]->product_category_id);
        $prodCatTitleUnserialized = unserialize($prod_cat_data[0]->category_title);

        $prod_subcat_data = AdminBaseController::getColumn('subcategories', 'id','=', $product_data[0]->product_subcategory_id);
        $subcatTitleUnserialized = unserialize($prod_subcat_data[0]->subcategory_title);
        
        $cat_data = AdminBaseController::getAllOrderBy('categories', 'category_title', 'asc');
        $cat_length = count($cat_data);
        
        for($i = 0; $i < $cat_length; $i++) {
            $catTitlesUnserialized[$i] = unserialize($cat_data[$i]->category_title);
        }
        
        $subcat_data = AdminBaseController::getAllOrderBy('subcategories', 'subcategory_title', 'asc');
        $subcat_length = count($subcat_data);
        
        for($i = 0; $i < $subcat_length; $i++) {
            $subcatTitlesUnserialized[$i] = unserialize($subcat_data[$i]->subcategory_title);
        }
       
        if(empty($page_data->logo_img)) {
            return $this->c->view->render($response, '/admin/product/edit_product.twig', [
                'products' => $product_data,
                'product_id' => $product_data[0]->id,
                //'product_id' => $product_id,
                'product_title' => $productTitlesUnserialized,
                'product_desc' => $productDescsUnserialized,
                'product_order' => $product_data[0]->product_order,
                'product_price' => $product_data[0]->product_price,
                'product_time_todo' => $product_data[0]->product_time_todo,
                'product_foto' => $product_data[0]->product_foto,
                'product_active' => $product_data[0]->product_active,
                'product_featured' => $product_data[0]->product_featured,
                'product_cat_id' => $product_data[0]->product_category_id,
                'product_cat_name' => $prodCatTitleUnserialized,
                'product_subcat_id' => $product_data[0]->product_subcategory_id,
                'product_slug' => $product_data[0]->product_slug,                
                'product_subcat_name' => $subcatTitleUnserialized,
                'product_subcat_id' => $prod_subcat_data[0]->id,
                'categories' => $cat_data,
                'cat_titles' => $catTitlesUnserialized,
                'subcategories' => $subcat_data,
                'subcat_titles' => $subcatTitlesUnserialized,
                //'exists' =>$cat_exists,
                'product_path' => '/restaurant-slim-v1/public/img/product/',
                //'product_path' => '/restaurant-v1_1/public/img/product/' // linux
                'logo' => $pageLogoNames,
                'logo_img' => '',
                'title' => $pageTitles, 
                'page' => $pageNames,
                'path' => ''
            ]);
        }
        
        return $this->c->view->render($response, '/admin/product/edit_product.twig', [
            'products' => $product_data,
            'product_id' => $product_data[0]->id,
            //'product_id' => $product_id,
            'product_title' => $productTitlesUnserialized,
            'product_desc' => $productDescsUnserialized,
            'product_order' => $product_data[0]->product_order,
            'product_price' => $product_data[0]->product_price,
            'product_time_todo' => $product_data[0]->product_time_todo,
            'product_foto' => $product_data[0]->product_foto,
            'product_active' => $product_data[0]->product_active,
            'product_featured' => $product_data[0]->product_featured,
            'product_cat_id' => $product_data[0]->product_category_id,
            'product_cat_name' => $prodCatTitleUnserialized,
            'product_subcat_name' => $subcatTitleUnserialized,
            'product_subcat_id' => $prod_subcat_data[0]->id,
            'product_slug' => $product_data[0]->product_slug,
            'categories' => $cat_data,
            'cat_titles' => $catTitlesUnserialized,
            'subcategories' => $subcat_data,
            'subcat_titles' => $subcatTitlesUnserialized,
            //'exists' =>$cat_exists,
            'product_path' => '/restaurant-slim-v1/public/img/product/',
            //'product_path' => '/restaurant-v1_1/public/img/product/', // linux
            'logo' => $pageLogoNames,
            'logo_img' => $page_data->logo_img,
            'title' => $pageTitles, 
            'page' => $pageNames,
            'path' => '/restaurant-slim-v1/public/img/logo/'
            //'path' => '/restaurant-v1_1/public/img/logo/' // linux
       ]);
    }

    public function postEditedProduct($request, $response)
    {
        $productNameGr = $request->getParam('productname_gr');
        $productDescGr = $request->getParam('productdescription_gr');
        $productNameEn = $request->getParam('productname_en');
        $productDescEn = $request->getParam('productdescription_en');
        $productNamesSerialized = serialize([[$productNameGr], [$productNameEn]]);
        $productDescsSerialized = serialize([[$productDescGr], [$productDescEn]]);

        $productPosition = $request->getParam('productorder');
        $productActive = $request->getParam('active');
        $productPrice = $request->getParam('productprice');
        $productFeatured = $request->getParam('productfeatured');
        $productSlug = $request->getParam('productslug');
        $productTimetodo = $request->getParam('producttimetodo');
        $product_id = $request->getParam('aProduct');
        $cat_id = $request->getParam('selectcategory');
        $subcat_id = $request->getParam('selectsubcategory');
        
        $page_data = AdminBaseController::initPage($request, $response);
        $data = AdminBaseController::getColumn('products', 'id', '=', $product_id);
        
        if(is_null($productActive)) {
            $active = 0;
        } else {
            $active = 1;
        }        

        if(is_null($productFeatured)) {
            $featured = 0;
        } else {
            $featured = 1;
        }
        
        $dir = $this->c->get('product_upload_directory');

        $files = $request->getUploadedFiles();// no image comes
       
        $file = $files['prodimg'];

        $db_product_img = is_null($data[0]->product_foto);false:true; // false = image exist in db

        switch ($file->getError()) { // check file input
            case 0: //input has a filename
                switch ($db_product_img) { // check if database has product img
                    case TRUE: // input has file, db is empty)
                    case FALSE: // input has file, db has image 
                        $uploadFileName = $file->getClientFilename();
                        $file->moveTo("$dir/$uploadFileName");

                        $isInserted = AdminProduct::where('id', $product_id)->update([
                            'product_title' => $productNamesSerialized,
                            'product_desc' => $productDescsSerialized,
                            'product_order' => $productPosition,
                            'product_foto' => $uploadFileName,
                            'product_featured' => $featured,
                            'product_price' => $productPrice,
                            'product_time_todo' => $productTimetodo,
                            'product_slug' =>$productSlug,
                            'product_active' => $active,     
                            'product_subcategory_id' => $subcat_id,
                            'product_category_id' => $cat_id,
                        ]);

                        if ($isInserted) {
                            // TODO: send sucess message
                        } else {
                            // TODO: send error message
                        }
                        break; // db is empty
                    default :
                        die('db img may be null');
                        break;
                } // check if database has product img 
                break; // input has filename, and db has img
            case 4: // input is empty
                switch ($db_product_img) { // check if database has category img
                    case TRUE: // input empty and db empty
//                        $isUpdated = AdminCategory::where('id', $cat_id)->update([
//                            'category_title' => $catName,
//                            'category_desc' => $catDesc,
//                            'category_position' => $catPosition,
//                            //'category_foto' => $uploadFileName,
//                            'category_active' => $catActive,     
//                            'page_id' => $page_data->id
//                        ]);
                        die('input empty - db empty');
                        break; // db empty
                    case FALSE: // input empty and db has image
                        $isUpdated = AdminProduct::where('id', $product_id)->update([
                            'product_title' => $productNamesSerialized,
                            'product_desc' => $productDescsSerialized,
                            'product_order' => $productPosition,
                            'product_featured' => $featured,
                            'product_price' => $productPrice,
                            'product_time_todo' => $productTimetodo,
                            'product_slug' =>$productSlug,
                            'product_active' => $active,     
                            'product_subcategory_id' => $subcat_id,
                            'product_category_id' => $cat_id,
                        ]);
                        break; // db has img 
                    default :
                        die('db img maybe null');
                        break;
                }
                break; // input is empty and db has logo
            default :
                var_dump($file->getError());
                die('default');
                break;
        }

        return $response->withRedirect($this->c->router->pathFor('show_products'));
    }
}