<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\AdminBaseController;

use App\Controllers\Admin\Page\AdminPageController;

use App\Models\Admin\AdminUser;

use App\Models\Admin\AdminPage;

use App\Models\Admin\AdminUserRole;

use Respect\Validation\Validator as v;


/**
 * Description of AdminHomeController
 *
 * @author Comtech
 */
class AdminHomeController extends AdminBaseController 
{

    public function getSignOut($request, $response)
    {
        $this->c->auth->logout();
        return $response->withRedirect($this->c->router->pathFor('admin_signin'));
    }
    
    public function getSignIn($request, $response) 
    {
        return $this->c->view->render($response, '/admin/signin.twig');
    }

    public function postSignIn($request, $response) 
    {
        $auth = $this->c->auth->attempt($request->getParam('username'), $request->getParam('password'));

        if(!$auth) {
            return $response->withRedirect($this->c->router->pathFor('admin_signin'));
        }
        
        return $response->withRedirect($this->c->router->pathFor('admin_home'));
    }

    public function getSignUp($request, $response)
    {
        $page_data = AdminBaseController::initPage($request, $response);
        
        $pageTitles = unserialize($page_data->page_title);
        $pageLogoNames = unserialize($page_data->logo_name);
        
        if(empty($page_data->logo_img)) {
            return $this->c->view->render($response, '/admin/signup.twig', [
                'logo' => $pageLogoNames,
                'title' => $pageTitles,
                'path' => ''
            ]);                    
        }
        
        return $this->c->view->render($response, '/admin/signup.twig', [
            'logo' => $pageLogoNames,
            'logo_img' => $page_data->logo_img,
            'title' => $pageTitles,
            'path' => '/restaurant-slim-v1/public/img/logo/'
            //'path' => '/restaurant-v1_1/public/img/logo/' //linux
            ]);
    }
    
    public function postSignUp($request, $response)
    {
        $page = AdminBaseController::getColumn('pages', 'id', '=', 1);
        $role = AdminBaseController::getColumn('roles', 'role_id', '=', 0);

        $validation = $this->c->validator->validate($request, [
            'surname' => v::notEmpty()->length(3, 50),
            'username' => v::noWhitespace()->notEmpty()->alnum(),
            'password' => v::noWhitespace()->notEmpty()->alnum(),
        ]);

        if($validation->failed()) {
            return $response->withRedirect($this->c->router->pathFor('admin_signup'));
        }
        
        $admin_user = AdminUser::insertGetId([
            'name' => $request->getParam('surname'),
            'username' => $request->getParam('username'),
            'password' => password_hash($request->getParam('password'), PASSWORD_DEFAULT),
            'page_id' => $page[0]->id,
            'role_id' => $role[0]->id
        ]);
        
        
        //$this->c->auth->attempt($admin_user->username, $request->getParam('password'));
        
        return $response->withRedirect($this->c->router->pathFor('admin_home'));
    }

    public function adminHome($request, $response) 
    {
        $page_data = AdminBaseController::initPage($request, $response);
        
        $pageTitles = unserialize($page_data->page_title);
        $pageLogoNames = unserialize($page_data->logo_name);
        
        if(empty($page_data->logo_img)) {
            return $this->c->view->render($response, '/admin/admin_home.twig', [
                'logo' => $pageLogoNames,
                'logo_img' => '',
                'title' => $pageTitles,
                'path' => ''
            ]);
        }
            return $this->c->view->render($response, '/admin/admin_home.twig', [
                'logo' => $pageLogoNames,
                'logo_img' => $page_data->logo_img,
                'title' => $pageTitles,
                'path' => '/restaurant-slim-v1/public/img/logo/'
                //'path' => '/restaurant-v1_1/public/img/logo/' // linux
            ]);
    }
}