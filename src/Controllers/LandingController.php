<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Response;

class LandingController extends Controller
{
    public function index(): void
    {
        $this->render('landing');
    }

    public function comingSoon(): void
    {
        $this->flash('info', 'Coming soon! We\'re working on this feature.');
        Response::redirect('/');
    }
}