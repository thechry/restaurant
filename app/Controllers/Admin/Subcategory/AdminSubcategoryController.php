<?php

namespace App\Controllers\Admin\Subcategory;

use App\Controllers\Admin\AdminBaseController;

use App\Models\Admin\AdminSubcategory;

use Slim\Http\UploadedFile;

use Respect\Validation\Validator as v;

use App\Models\Admin\AdminCategory;

/**
 * Description of AdminCategoryController
 *
 * @author Comtech
 */
class AdminSubcategoryController extends AdminBaseController 
{
   /**
   * Description of insertCategory
   * Loads category template and update page data from database
   * 
   * @author Comtech
   */
    public function insertSubcategory($request, $response)
    {
        // Update page settings block
        $page_data = AdminBaseController::initPage($request, $response);
        $categories = AdminCategory::all();

        $pageNames = unserialize($page_data->page_name);
        $pageTitles = unserialize($page_data->page_title);
        $pageLogoNames = unserialize($page_data->logo_name);
        
        $length = count($categories);
        for($i = 0; $i < $length; $i++) {
            $categoriesTitleUnserialized[$i] = unserialize($categories[$i]->category_title);
            //$categoriesDescsUnserialized[$i] = unserialize($categories[$i]->category_desc);
        }

        if(empty($page_data->logo_img)) {
             return $this->c->view->render($response, '/admin/subcategory/insert_subcategory.twig', [
                 'logo' => $pageLogoNames,
                 'logo_img' => '',
                 'title' => $pageTitles, 
                 'page' => $pageNames,
                 'path' => '',
                 'categories' => $categories,
                 'cat_titles' => $categoriesTitleUnserialized,
                 //'cat_descs' => $categoriesDescsUnserialized,
                 'page_id' => $page_data->id,
                 'size' => $length,
             ]);
         }

        return $this->c->view->render($response, '/admin/subcategory/insert_subcategory.twig', [
                'logo' => $pageLogoNames,
                'logo_img' => $page_data->logo_img,
                'title' => $pageTitles, 
                'page' => $pageNames,
                'path' => '/restaurant-slim-v1/public/img/logo/',
                //'path' => '/restaurant-v1_1/public/img/logo/',  // linux
                'categories' => $categories,
                'cat_titles' => $categoriesTitleUnserialized,
                //'cat_descs' => $categoriesDescsUnserialized,
                'page_id' => $page_data->id,
                'size' => $length,
        ]);
        // End Update page settings block
    }
   
    public function postSubcategory($request, $response)
   {    
        $validation = $this->c->validator->validate($request, [
            'subcategoryname_gr' => v::notEmpty(),
            'subcategorydescription_gr' => v::notEmpty(),
            'subcategoryorder' => v::digit(),
            'subcategoryname_en' => v::notEmpty(),
            'subcategorydescription_en' => v::notEmpty(),
        ]);

        if($validation->failed()) {
            //echo "validation error";
            return $response->withRedirect($this->c->router->pathFor('insert_subcategory_form'));
        }
          
        $page = AdminBaseController::getColumn('pages', 'id', '=', 1);
        
        $data = $request->getParams();
        $subcategoryNames = [[$data['subcategoryname_gr']], [$data['subcategoryname_en']]];
        $subcategoriesTitle = serialize($subcategoryNames);
        $subcategoryDescs = [[$data['subcategorydescription_gr']], [$data['subcategorydescription_en']]];
        $subcategoriesDescs = serialize($subcategoryDescs);

        $dir = $this->c->get('subcategory_upload_directory');

        $files = $request->getUploadedFiles();
        
        $file = $files['subcatimg'];
        
        if($file->getError() === UPLOAD_ERR_OK) {
            $uploadFileName = $file->getClientFilename();
            $file->moveTo("$dir/$uploadFileName");
        }

        if(is_null($data['subcatactive'])) {
            $active = 0;
        } else {
            $active = 1;
        }        
        
        $subcat_slug = str_slug($data['subcategoryname_gr']);
        
        $data_inserted = AdminSubCategory::insertGetId([
            'subcategory_title' => $subcategoriesTitle,
            'subcategory_desc' => $subcategoriesDescs,
            'subcategory_active' => $active,
            'subcategory_position' => $data['subcategoryorder'],
            'subcategory_foto' => $uploadFileName,
            'subcategory_slug' => $subcat_slug,
            'subcategory_page_id' => $page[0]->id,
            'subcategory_category_id' => $data['selectcategory'],
        ]);
        
       return $response->withRedirect($this->c->router->pathFor('show_subcategories'));
   }
   
   public function listSubcategory($request, $response)
   {
        $page_data = AdminBaseController::initPage($request, $response);
        
        $pageNames = unserialize($page_data->page_name);
        $pageTitles = unserialize($page_data->page_title);
        $pageLogoNames = unserialize($page_data->logo_name);
        
        $subcat_exists = 0;
        $subcat_data = AdminSubcategory::all();

        if(!is_null($subcat_data[0])) {
            $subcat_exists = 1;
        }
        $length = count($subcat_data);
        
        for($i = 0; $i < $length; $i++) {
            $subcategoriesTitlesUnserialized[$i] = unserialize($subcat_data[$i]->subcategory_title);
            $subcategoriesDescsUnserialized[$i] = unserialize($subcat_data[$i]->subcategory_desc);
            $subcategoriesCategoryId[$i] = $subcat_data[$i]->subcategory_category_id;
            $cat_names[$i] = AdminCategory::select('category_title')->where('id', '=', $subcategoriesCategoryId[$i])->get();
            $cat_names_unserialised[$i] = unserialize($cat_names[$i][0]->category_title);
            //var_dump($cat_names_unserialised);
        }
        //var_dump($cat_names_unserialised[2][0][0]);
   //die();
        
        if(empty($page_data->logo_img)) {
             return $this->c->view->render($response, '/admin/subcategory/list_subcategory.twig', [
                 'subcategories' => $subcat_data,
                 'subcat_titles' => $subcategoriesTitlesUnserialized,
                 'subcat_descs' => $subcategoriesDescsUnserialized,
                 'cat_name' => $cat_names_unserialised,
                 'exists' => $subcat_exists,
                 'subcat_path' => '/restaurant-slim-v1/public/img/subcategory/',
                 //'subcat_path' => '/restaurant-v1_1/public/img/subcategory/', // linux
                 'logo' => $pageLogoNames,
                 'logo_img' => '',
                 'title' => $pageTitles, 
                 'page' => $pageNames,
                 'path' => ''
             ]);
         }        
       
        return $this->c->view->render($response, '/admin/subcategory/list_subcategory.twig', [
            'subcategories' => $subcat_data,
            'subcat_titles' => $subcategoriesTitlesUnserialized,
            'subcat_descs' => $subcategoriesDescsUnserialized,
            'cat_name' => $cat_names_unserialised,
            'exists' => $subcat_exists,
            'subcat_path' => '/restaurant-slim-v1/public/img/subcategory/',
             //'subcat_path' => '/restaurant-v1_1/public/img/subcategory/', // linux
            'logo' => $pageLogoNames,
            'logo_img' => $page_data->logo_img,
            'title' => $pageTitles, 
            'page' => $pageNames,
            'path' => '/restaurant-slim-v1/public/img/logo/',
             //'path' => '/restaurant-v1_1/public/img/logo/', // linux
        ]);
   }
   
   public function editSubcategory($request, $response) 
   {
        $page_data = AdminBaseController::initPage($request, $response);

        $pageNames = unserialize($page_data->page_name);
        $pageTitles = unserialize($page_data->page_title);
        $pageLogoNames = unserialize($page_data->logo_name);
       
        $subcat_id = $request->getParam('aSubcategory'); // Comes from list subcategories
        $subcat_data = AdminBaseController::getColumn('subcategories', 'id', '=', $subcat_id);

        $subcategoryTitleUnserialized = unserialize($subcat_data[0]->subcategory_title);
        $subcategoryDescUnserialized = unserialize($subcat_data[0]->subcategory_desc);
    
        $cat_id = AdminSubcategory::select('subcategory_category_id')->where('id', '=', $subcat_id)->get();

        $cat_name = AdminBaseController::getColumn('categories', 'id', '=', $cat_id[0]->subcategory_category_id);
        
        $categoryNameUnserialized = unserialize($cat_name[0]->category_title);
        
        $cat_data = AdminBaseController::getAllOrderBy('categories', 'category_title', 'asc');
        $length = count($cat_data);
        
        for($i = 0; $i < $length; $i++) {
            $categoryTitleUnserialized[$i] = unserialize($cat_data[$i]->category_title);
        }
  
        if(empty($page_data->logo_img)) {
            return $this->c->view->render($response, '/admin/subcategory/edit_subcategory.twig', [
                'subcategories' => $subcat_data,
                'subcat_id' => $subcat_data[0]->id,
                'subcat_title' => $subcategoryTitleUnserialized,
                'subcat_desc' => $subcategoryDescUnserialized,
                'subcat_order' => $subcat_data[0]->subcategory_position,
                'subcat_foto' => $subcat_data[0]->subcategory_foto,
                'subcat_active' => $subcat_data[0]->subcategory_active,
                'subcat_slug' => $subcat_data[0]->subcategory_slug,
                'subcat_cat_id' => $cat_id[0]->subcategory_category_id,                
                'subcat_cat_name' => $categoryNameUnserialized,
                'categories' => $cat_data,
                'cat_name' => $categoryTitleUnserialized,
                //'exists' =>$cat_exists,
                'subcat_path' => '/restaurant-slim-v1/public/img/subcategory/',
                //'subcat_path' => '/restaurant-v1_1/public/img/subcategory/', // linux
                'logo' => $pageLogoNames,
                'logo_img' => '',
                'title' => $pageTitles, 
                'page' => $pageNames,
                'path' => ''
            ]);
        }

        return $this->c->view->render($response, '/admin/subcategory/edit_subcategory.twig', [
            'subcategories' => $subcat_data,
            'subcat_id' => $subcat_data[0]->id,
            'subcat_title' => $subcategoryTitleUnserialized,
            'subcat_desc' => $subcategoryDescUnserialized,
            'subcat_order' => $subcat_data[0]->subcategory_position,
            'subcat_foto' => $subcat_data[0]->subcategory_foto,
            'subcat_active' => $subcat_data[0]->subcategory_active,
            'subcat_slug' => $subcat_data[0]->subcategory_slug,
            'subcat_cat_id' => $cat_id[0]->subcategory_category_id,
            'subcat_cat_name' => $categoryNameUnserialized,
            'categories' => $cat_data,
            'cat_name' => $categoryTitleUnserialized,
            //'exists' =>$cat_exists,
            'subcat_path' => '/restaurant-slim-v1/public/img/subcategory/',
            //'subcat_path' => '/restaurant-v1_1/public/img/subcategory/', // linux
            'logo' => $pageLogoNames,
            'logo_img' => $page_data->logo_img,
            'title' => $pageTitles, 
            'page' => $pageNames,
            'path' => '/restaurant-slim-v1/public/img/logo/'
            //'path' => '/restaurant-v1_1/public/img/logo/' // linux
       ]);           
   }
   
   public function postEditedSubcategory($request, $response)
   {
        $subcatNameGr = $request->getParam('subcategoryname_gr');
        $subcatDescGr = $request->getParam('subcategotydescription_gr');
        $subcatNameEn = $request->getParam('subcategoryname_en');
        $subcatDescEn = $request->getParam('subcategotydescription_en');
        $subcatPosition = $request->getParam('subcategoryorder');
        $subcatActive = $request->getParam('active');
        $subcatSlug = $request->getParam('subcategoryslug');
        $subcat_id = $request->getParam('subcatid');
        $cat = $request->getParam('selectcategory');
        
        $subcategoriesNameSerialized = serialize([[$subcatNameGr], [$subcatNameEn]]);
        $subcategoriesDescSerialized = serialize([[$subcatDescGr], [$subcatDescEn]]);
        
        $page_data = AdminBaseController::initPage($request, $response);
        $data = AdminBaseController::getColumn('subcategories', 'id', '=', $subcat_id);

        if(is_null($subcatActive)) {
            $active = 0;
        } else {
            $active = 1;
        }        

        $dir = $this->c->get('subcategory_upload_directory');

        $files = $request->getUploadedFiles();
       
        $file = $files['subcatimg'];

        $db_subcat_img = is_null($data[0]->subcategory_foto);false:true; // false = image exist in db

        switch ($file->getError()) { // check file input
            case 0: //input has a filename
                switch ($db_subcat_img) { // check if database has category img
                    case TRUE: // input has file db is empty)
                    case FALSE: // input has file db has image 
                        $uploadFileName = $file->getClientFilename();
                        $file->moveTo("$dir/$uploadFileName");

                        $isInserted = AdminSubcategory::where('id', $subcat_id)->update([
                            'subcategory_title' => $subcategoriesNameSerialized,
                            'subcategory_desc' => $subcategoriesDescSerialized,
                            'subcategory_position' => $subcatPosition,
                            'subcategory_foto' => $uploadFileName,
                            'subcategory_slug' =>$subcatSlug,
                            'subcategory_active' => $active,     
                            'subcategory_page_id' => $page_data->id,
                            'subcategory_category_id' => $cat,
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
                switch ($db_subcat_img) { // check if database has category img
                    case TRUE: // input empty and db empty
                        die('input empty - db empty');
                        break; // db empty
                    case FALSE: // input empty and db has image
                        $isUpdated = AdminSubcategory::where('id', $subcat_id)->update([
                            'subcategory_title' => $subcategoriesNameSerialized,
                            'subcategory_desc' => $subcategoriesDescSerialized,
                            'subcategory_position' => $subcatPosition,
                            'subcategory_slug' =>$subcatSlug,
                            'subcategory_active' => $active,
                            'subcategory_page_id' => $page_data->id,
                            'subcategory_category_id' => $cat,
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

         return $response->withRedirect($this->c->router->pathFor('show_subcategories'));
   }
}
