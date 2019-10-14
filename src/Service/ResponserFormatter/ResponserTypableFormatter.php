<?php


namespace App\Service\ResponserFormatter;

use Symfony\Component\HttpFoundation\Response;

interface ResponserTypableFormatter
{
    public function format(string $content) : Response;
    public function type() : string;
}