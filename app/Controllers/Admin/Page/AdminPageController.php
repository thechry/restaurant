<?php

namespace App\Controllers\Admin\Page;

use App\Controllers\Admin\AdminBaseController;
use App\Controllers\Admin\AdminHomeController;
use Respect\Validation\Validator as v;
use App\Models\Admin\AdminPage;

/**
 * Description of AdminPageController
 *
 * @author Comtech
 */
class AdminPageController extends AdminBaseController {

    private $error;

    public function createPage($request, $response) {
        $page_data = AdminBaseController::initPage($request, $response);
        
        $pageName = $page_data->page_name;
        $pageTitles = unserialize($page_data->page_title);
        $pageLogoNames = unserialize($page_data->logo_name);
                
        if (empty($page_data->logo_img)) {
            return $this->c->view->render($response, '/admin/page/page_data.twig', [
                        'logo' => $pageLogoNames,
                        'empty_logo_img' => 'Εικόνα Logo',
                        'title' => $pageTitles,
                        'page' => $pageName,
                        //'path' => '',
            ]);
        }

        return $this->c->view->render($response, '/admin/page/page_data.twig', [
                    'logo' => $pageLogoNames,
                    'logo_img' => $page_data->logo_img,
                    'title' => $pageTitles,
                    'page' => $pageName,
                    'path' => '/restaurant-slim-v1/public/img/logo/'
                    //'path' => '/restaurant-v1_1/public/img/logo/' //linux
        ]);
    }

    public function postPage($request, $response) {
        $pageName = $request->getParam('pagename');
        $logoNameGr = $request->getParam('logoname_gr');
        $pageTitleGr = $request->getParam('pagetitle_gr');
        //$pageNameEn = $request->getParam('pagename_en');
        $logoNameEn = $request->getParam('logoname_en');
        $pageTitleEn = $request->getParam('pagetitle_en');

        $validation = $this->c->validator->validate($request, [
            'pagename' => v::notEmpty()->length(3, 50),
            'pagetitle_gr' => v::notEmpty()->length(3, 50),
            'logoname_gr' => v::notEmpty()->length(3, 50),
            //'pagename_en' => v::notEmpty()->length(3, 50),
            'pagetitle_en' => v::notEmpty()->length(3, 50),
            'logoname_en' => v::notEmpty()->length(3, 50),
        ]);
        
        //$pageNames = [[$pageNameGr], [$pageNameEn]];
        //$pageNamesSerialized = serialize($pageNames);
        
        $logoNames = [[$logoNameGr], [$logoNameEn]];
        $logoNamesSerialized = serialize($logoNames);
        
        $pageTitles = [[$pageTitleGr], [$pageTitleEn]];
        $pageTitleSerialized = serialize($pageTitles);
        
        if ($validation->failed()) {
            //echo "validation error";
            return $response->withRedirect($this->c->router->pathFor('page_settings_form'));
        }

        $page_data = AdminBaseController::initPage($request, $response);
        $page = AdminPage::find(1);

        $dir = $this->c->get('logo_upload_directory');
        $files = $request->getUploadedFiles();
        $file = $files['logoimg'];
        $db_logo_img = isset($page_data->logo_img);


        switch ($file->getError()) { // check file input
            case 0: //input has a filename
                switch ($db_logo_img) { // check if database has logo
                    case TRUE: // CASE UPDATE LOGO (both input and db has file)
                        $uploadFileName = $file->getClientFilename();
                        $file->moveTo("$dir/$uploadFileName");

                        $isUpdated = AdminPage::where('id', $page->id)->update([
                            'page_name' => $pageName,
                            'logo_name' => $logoNamesSerialized,
                            'logo_img' => $uploadFileName,
                            'page_title' => $pageTitleSerialized,
                        ]);
                        break; // db has logo
                    case FALSE:
                        $uploadFileName = $file->getClientFilename();
                        $file->moveTo("$dir/$uploadFileName");
                        $isUpdated = AdminPage::where('id', $page->id)->update([
                            'page_name' => $pageName,
                            'logo_name' => $logoNamesSerialized,
                            'logo_img' => $uploadFileName,
                            'page_title' => $pageTitleSerialized,
                        ]);
                        if ($isUpdated) {
                            // TODO: send sucess message
                        } else {
                            // TODO: send error message
                        }
                        break; // db is empty
                    default :
                        die('db img maybe null');
                        break;
                } // check if database has logo 
                break; // input has filename and db has logo
            case 4: // input is empty
                switch ($db_logo_img) { // check if database has logo
                    case TRUE: // CASE UPDATE DATA NOT LOGO (input empty and db has file)
                        $isUpdated = AdminPage::where('id', $page->id)->update([
                            'page_name' => $pageName,
                            'logo_name' => $logoNamesSerialized,
                            'page_title' => $pageTitleSerialized,
                        ]);
                        break; // db has logo
                    case FALSE:
                        var_dump($file->getError());
                        die('input empty - db empty');
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

        return $response->withRedirect($this->c->router->pathFor('admin_home'));
        /*
          return $this->c->view->render($response, '/admin/admin_home.twig', [
          'error' => $this->error,
          ]);
         */
    }

}