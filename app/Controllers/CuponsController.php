<?php

namespace App\controllers;

use App\exceptions\exceptionCustom;

class CuponsController
{
    /**
     * @throws exceptionCustom
     */
    function index(): void
    {
        Controller::view("cupons");
    }
}