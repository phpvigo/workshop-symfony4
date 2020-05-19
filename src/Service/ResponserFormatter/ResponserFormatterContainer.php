<?php


namespace App\Service\ResponserFormatter;

use Symfony\Component\HttpFoundation\Response;

interface ResponserFormatterContainer
{
    public function format(string $format, string $content) : Response;
}
