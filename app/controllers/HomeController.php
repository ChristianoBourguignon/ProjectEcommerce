<?php

namespace App\controllers;

use App\exceptions\exceptionCustom;

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

    /**
     * @throws exceptionCustom
     */
    public function sobre():void
    {
        Controller::view("about");
    }
}