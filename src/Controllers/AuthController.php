<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Response;
use App\Core\Validator;

class AuthController extends Controller
{
    public function loginForm(): void
    {
        if (Auth::checkAdmin()) {
            Response::redirect('/admin/dashboard');
            return;
        }
        
        $this->render('admin/login');
    }

    public function login(): void
    {
        $this->requireCsrf();
        
        $validator = Validator::make($_POST)
            ->required('email')
            ->email('email')
            ->required('password');
        
        if ($validator->fails()) {
            $this->flash('error', $validator->firstError());
            Response::redirect('/admin/login');
            return;
        }
        
        if (!Auth::login($_POST['email'], $_POST['password'])) {
            $this->flash('error', 'Invalid email or password');
            Response::redirect('/admin/login');
            return;
        }
        
        $this->flash('success', 'Welcome back!');
        Response::redirect('/admin/dashboard');
    }

    public function logout(): void
    {
        $this->requireCsrf();
        Auth::logout();
        $this->flash('success', 'You have been logged out');
        Response::redirect('/');
    }
}