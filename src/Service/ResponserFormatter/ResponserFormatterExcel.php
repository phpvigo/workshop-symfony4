<?php


namespace App\Service\ResponserFormatter;


use App\Service\ResponserFormatter\ResponserTypableFormatter;
use Symfony\Component\HttpFoundation\Response;

class ResponserFormatterExcel implements ResponserTypableFormatter
{
    public function format(string $content): Response
    {
        $response = new Response();
        $response->setContent($content);
        $response->headers->set('Content-Type', 'application/octet-stream');
        $response->headers->set('Content-Disposition', 'attachment; filename="hashtag' . (new \DateTimeImmutable())->format('YmdHis') . '.xlsx"');
        return $response;
    }

    public function type(): string
    {
        return 'excel';
    }

}