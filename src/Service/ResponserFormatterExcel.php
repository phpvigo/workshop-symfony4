<?php


namespace App\Service;


use Symfony\Component\HttpFoundation\Response;

class ResponserFormatterExcel implements ResponserTypableFormatter
{
    public function format(): Response
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="hashtag' . (new \DateTimeImmutable())->format('YmdHis') . '.xlsx"');
        $response->sendHeaders();
        return $response;
    }

    public function type(): string
    {
        return 'excel';
    }

}