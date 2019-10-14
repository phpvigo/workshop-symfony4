<?php


namespace App\Service;


use Symfony\Component\HttpFoundation\Response;

class ResponserFormatterCsv implements ResponserTypableFormatter
{
    public function format(string $content): Response
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/csv');
        $response->setContent($content);
        return $response;
    }

    public function type(): string
    {
        return 'csv';
    }

}