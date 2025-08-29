<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class PagesController extends Controller
{
    public function terms(): void
    {
        $this->render('terms', [
            'title' => 'Terms & Conditions',
            'termsText' => TERMS_TEXT,
            'termsUrl' => TERMS_URL
        ]);
    }
}
