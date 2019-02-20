<?php

namespace App\Auth;

use App\Models\Admin\AdminUser;

/**
 * Description of Auth
 *
 * @author Comtech
 */
class Auth {
    
    public function user() 
    {
        return AdminUser::find($_SESSION['username']);
    }
    
    public function check() 
    {
        return isset($_SESSION['username']);
    }
    
  /*
    public function checkTime()
    {
        return isset($_SESSION['time_logged']);
    }
   */
    
    public function attempt($username, $password) 
    {
        $user = AdminUser::where('username', $username)->first();

        //No matching user
        if(!$user) {
            return false;
        }
        
        // Matching user & password
        if(password_verify($password, $user->password)) {
            $_SESSION['username'] = $user->id;
//            $_SESSION['time_logged'] = time();

            return true;
        }
        
        // No matching password
        return false;
    }
    
    public function logout()
    {
        unset($_SESSION['username']);
        //unset($_SESSION['time_logged']);
    }
}