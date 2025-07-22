<?php

namespace App\Controllers;

use App\Exceptions\exceptionCustom;

class HomeController
{
    /**
     * @throws exceptionCustom
     */
    public function index():void
    {
        Controller::view("home");
    }

    /**
     * @throws exceptionCustom
     */
    public function notFound():void
    {
        Controller::view("404");
    }
}