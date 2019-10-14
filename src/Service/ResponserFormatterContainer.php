<?php


namespace App\Service;


use Symfony\Component\HttpFoundation\Response;

interface ResponserFormatterContainer
{
    public function format(string $format) : Response;
}