<?php


namespace App\Service;

use Symfony\Component\HttpFoundation\Response;

interface ResponserTypableFormatter
{
    public function format() : Response;
    public function type() : string;
}