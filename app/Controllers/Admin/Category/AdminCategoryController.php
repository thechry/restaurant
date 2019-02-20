<?php

namespace App\Controllers\Admin\Category;

use App\Controllers\Admin\AdminBaseController;

use App\Models\Admin\AdminCategory;

use Slim\Http\UploadedFile;

use Respect\Validation\Validator as v;

/**
 * Description of AdminCategoryController
 *
 * @author Comtech
 */
class AdminCategoryController extends AdminBaseController 
{
   /**
   * Description of insertCategory
   * Loads category template and update page data from database
   * 
   * @author Comtech
   */
   public function insertCategory($request, $response)
   {
       // Update page settings block
       $page_data = AdminBaseController::initPage($request, $response);
       
        $pageNames = unserialize($page_data->page_name);
        $pageTitles = unserialize($page_data->page_title);
        $pageLogoNames = unserialize($page_data->logo_name);

       if(empty($page_data->logo_img)) {
            return $this->c->view->render($response, '/admin/category/insert_category.twig', [
                'logo' => $pageLogoNames,
                'logo_img' => '',
                'title' => $pageTitles, 
                'page' => $pageNames,
                'path' => ''
            ]);
        }
       
       return $this->c->view->render($response, '/admin/category/insert_category.twig', [
            'logo' => $pageLogoNames,
            'logo_img' => $page_data->logo_img,
            'title' => $pageTitles, 
            'page' => $pageNames,
            'path' => '/restaurant-slim-v1/public/img/logo/'
            //'path' => '/restaurant-v1_1/public/img/logo/'  // linux
       ]);
       // End Update page settings block
   }
   
    public function postCategory($request, $response)
   {
          $validation = $this->c->validator->validate($request, [
            'categoryname_gr' => v::notEmpty(),
            'categorydescription_gr' => v::notEmpty(),
            'categoryorder' => v::digit(),
            'categoryname_en' => v::notEmpty(),
            'categorydescription_en' => v::notEmpty(),
        ]);

        if($validation->failed()) {
            //echo "validation error";
            return $response->withRedirect($this->c->router->pathFor('insert_category_form'));
        }
          
        $page = AdminBaseController::getColumn('pages', 'id', '=', 1);
        
        $data = $request->getParams();
        $categoryNames = [[$data['categoryname_gr']], [$data['categoryname_en']]];
        $categoryNamesSerialized = serialize($categoryNames);
        $categoryDescriptions = [[$data['categorydescription_gr']], [$data['categorydescription_en']]];
        $categoryDescriptionsSerialized = serialize($categoryDescriptions);

        $dir = $this->c->get('category_upload_directory');

        $files = $request->getUploadedFiles();
        
        $file = $files['catimg'];

        if($file->getError() === UPLOAD_ERR_OK) {
            //$filename = AdminBaseController::moveUploadedFile($dir, $uploadedFile);
            $uploadFileName = $file->getClientFilename();
            $file->moveTo("$dir/$uploadFileName");
        }

        if(empty($data['catactive'])) {
            $active = 0;
        } else {
            $active = 1;
        }        

        $cat_slug = str_slug($data['categoryname_gr']);

        $data_inserted = AdminCategory::insertGetId([
            'category_title' => $categoryNamesSerialized,
            'category_desc' => $categoryDescriptionsSerialized,
            'category_active' => $active,
            'category_position' => $data['categoryorder'],
            'category_foto' => $uploadFileName,
            'category_slug' => $cat_slug,
            'page_id' => $page[0]->id
        ]);
        
       return $response->withRedirect($this->c->router->pathFor('show_categories'));
   }
   
   public function listCategory($request, $response)
   {
        $page_data = AdminBaseController::initPage($request, $response);
        $pageNameUnserialized = unserialize($page_data->page_name);
        $logoNameUnserialized = unserialize($page_data->logo_name);
        $pageTitleUnserialized = unserialize($page_data->page_title);

        $cat_exists = 0;
        $cat_data = AdminCategory::all();
        
        if(!is_null($cat_data[0])) { // first db record
           $cat_exists = 1;
        }
       
        for($i = 0; $i < count($cat_data); $i++) {
           $categoryTitleUnserialized[$i] = unserialize($cat_data[$i]->category_title);
           $categoryDescriptionUnserialized[$i] = unserialize($cat_data[$i]->category_desc);
        }

        $length = count($categoryDescriptionUnserialized);
       
       if(empty($page_data->logo_img)) {
            return $this->c->view->render($response, '/admin/category/list_category.twig', [
                'categories' => $cat_data,
                'cat_title' => $categoryTitleUnserialized,
                'cat_desc' => $categoryDescriptionUnserialized,
                'size' => $length,
                'exists' =>$cat_exists,
                'cat_path' => '/restaurant-slim-v1/public/img/category/',
        	//'cat_path' => '/restaurant-v1_1/public/img/category/', // linux
                'logo' => $logoNameUnserialized,
                'logo_img' => '',
                'title' => $pageTitleUnserialized, 
                'page' => $pageNameUnserialized,
                'path' => ''
            ]);
        }        
       
       return $this->c->view->render($response, '/admin/category/list_category.twig', [
            'categories' => $cat_data,
            'cat_title' => $categoryTitleUnserialized,
            'cat_desc' => $categoryDescriptionUnserialized,
            'size' => $length,
            'exists' =>$cat_exists,
            'cat_path' => '/restaurant-slim-v1/public/img/category/',
            //'cat_path' => '/restaurant-v1_1/public/img/category/', // linux
            'logo' => $logoNameUnserialized,
            'logo_img' => $page_data->logo_img,
            'title' => $pageTitleUnserialized, 
            'page' => $pageNameUnserialized,
            'path' => '/restaurant-slim-v1/public/img/logo/'
            //'path' => '/restaurant-v1_1/public/img/logo/' // linux
       ]);
   }
   
   public function editCategory($request, $response) 
   {
        $page_data = AdminBaseController::initPage($request, $response);

        $pageNameUnserialized = unserialize($page_data->page_name);
        $logoNameUnserialized = unserialize($page_data->logo_name);
        $pageTitleUnserialized = unserialize($page_data->page_title);
        
        $cat_id = $request->getParam('aCategory'); // Comes from list categories

        $cat_data = AdminBaseController::getColumn('categories', 'id', '=', $cat_id);
//        var_dump($cat_data[0]);
//        die();
        $categoryTitleUnserialized = unserialize($cat_data[0]->category_title);
        $categoryDescriptionUnserialized = unserialize($cat_data[0]->category_desc);
//        var_dump($categoryTitleUnserialized[1][0]);
//        die();
        if(empty($page_data->logo_img)) {
            return $this->c->view->render($response, '/admin/category/edit_category.twig', [
                'categories' => $cat_data,
                'cat_id' => $cat_data[0]->id,
                'cat_title' => $categoryTitleUnserialized,
                'cat_desc' => $categoryDescriptionUnserialized,
                'cat_order' => $cat_data[0]->category_position,
                'cat_foto' => $cat_data[0]->category_foto,
                'cat_active' => $cat_data[0]->category_active,
                'cat_slug' => $cat_data[0]->category_slug,
                //'exists' =>$cat_exists,
                'cat_path' => '',
                'logo' => $logoNameUnserialized,
                'logo_img' => '',
                'title' => $pageTitleUnserialized, 
                'page' => $pageNameUnserialized,
                'path' => ''
            ]);
        }

        return $this->c->view->render($response, '/admin/category/edit_category.twig', [
            'categories' => $cat_data,
            'cat_id' => $cat_data[0]->id,
            'cat_title' => $categoryTitleUnserialized,
            'cat_desc' => $categoryDescriptionUnserialized,
            'cat_order' => $cat_data[0]->category_position,
            'cat_foto' => $cat_data[0]->category_foto,
            'cat_active' => $cat_data[0]->category_active,
            'cat_slug' => $cat_data[0]->category_slug,
            //'exists' =>$cat_exists,
            'cat_path' => '/restaurant-slim-v1/public/img/category/',
            //'cat_path' => '/restaurant-v1_1/public/img/category/', // linux
            'logo' => $logoNameUnserialized,
            'logo_img' => $page_data->logo_img,
            'title' => $pageTitleUnserialized, 
            'page' => $pageNameUnserialized,
            'path' => '/restaurant-slim-v1/public/img/logo/'
            //'path' => '/restaurant-v1_1/public/img/logo/' // linux
       ]);           
   }
   
   public function postEditedCategory($request, $response)
   {
        $catNameGr = $request->getParam('categoryname_gr');
        $catDescGr = $request->getParam('categorydescription_gr');
        $catNameEn = $request->getParam('categoryname_en');
        $catDescEn = $request->getParam('categorydescription_en');
        
        $catNamesSerialized = serialize([[$catNameGr], [$catNameEn]]);
        $catDescsSerialize = serialize([[$catDescGr], [$catDescEn]]);
        
//        var_dump($catDescsSerialize);
//        die();
//        
        $catPosition = $request->getParam('categoryorder');
        $catActive = $request->getParam('active');
        $catSlug = $request->getParam('categoryslug');
        $cat_id = $request->getPAram('catid');

        $page_data = AdminBaseController::initPage($request, $response);
        $data = AdminBaseController::getColumn('categories', 'id', '=', $cat_id);

        if(is_null($catActive)) {
            $active = 0;
        } else {
            $active = 1;
        }        

//        if(empty($data['categoryfeatuerd'])) {
//            $featured = 0;
//        } else {
//            $featured = 1;
//        }
        
        $dir = $this->c->get('category_upload_directory');
        
        $files = $request->getUploadedFiles();
        
        $file = $files['catimg'];

        $db_cat_img = is_null($data[0]->category_foto);false:true; // false = image exist in db
        //var_dump($db_cat_img);
        //die();
        switch ($file->getError()) { // check file input
            case 0: //input has a filename
                switch ($db_cat_img) { // check if database has category img
                    case TRUE: // input has file db is empty)
                    case FALSE: // input has file db has image 
                        $uploadFileName = $file->getClientFilename();
                        $file->moveTo("$dir/$uploadFileName");

                        $isInserted = AdminCategory::where('id', $cat_id)->update([
                            'category_title' => $catNamesSerialized,
                            'category_desc' => $catDescsSerialize,
                            'category_position' => $catPosition,
                            'category_foto' => $uploadFileName,
                            'category_active' => $active,
                            'category_slug' => $catSlug,
                            'page_id' => $page_data->id
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
                } // check if database has category img 
                break; // input has filename and db has logo
            case 4: // input is empty
                switch ($db_cat_img) { // check if database has category img
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
                        break; // db has logo
                    case FALSE: // input empty and db has image
                        $isUpdated = AdminCategory::where('id', $cat_id)->update([
                            'category_title' => $catNamesSerialized,
                            'category_desc' => $catDescsSerialize,
                            'category_position' => $catPosition,
                            //'category_foto' => $uploadFileName,
                            'category_active' => $active,
                            'category_slug' => $catSlug,
                            'page_id' => $page_data->id
                        ]);
                        break; // db logo is empty
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

        /*
        if($uploadedFile->getError() === UPLOAD_ERR_OK) {
            $filename = AdminBaseController::moveUploadedFile($dir, $uploadedFile);
        }

        if(is_null($request->getParam('active'))) {
            $active = 0;
        } else {
            $active = 1;
        }



            $category = AdminCategory::where('id', $cat_id)->update([
                'category_title' => $request->getParam('categoryname'),
                'category_desc' => $request->getParam('categotydescription'),
                'category_position' => $request->getParam('categoryorder'),
                'category_foto' => $filename,
                'category_active' => $active,     
                'category_foto' => $filename,
                'page_id' => $page_data->id
            ]);
*/
         return $response->withRedirect($this->c->router->pathFor('show_categories'));
   }
   
/*  
   public function deleteCategory($request, $response) 
   {
       var_dump($request->getParams());
       die();
         
       return $this->c->view->render($response, '/admin/category/delete_category.twig');
   }
 * 
 */
}
