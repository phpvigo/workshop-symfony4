<?php


namespace App\Service;

use Symfony\Component\HttpFoundation\Response;

class ResponserFormatter implements ResponserFormatterContainer
{
    /**
     * @var ResponserTypableFormatter[]
     */
    private $formaters;

    public function __construct(ResponserTypableFormatter ... $formaters)
    {
        $this->formaters = [];
        foreach ($formaters AS $formater) {
            $this->formaters[$formater->type()] = $formater;
        }
    }

    public function format(string $format, string $content): Response
    {
        $this->checkIfFormatIsAvailableOrFail($format);
        return $this->formaters[$format]->format($content);
    }

    private function checkIfFormatIsAvailableOrFail(string $format) : void
    {
        if (!array_key_exists($format, $this->formaters)) {
            throw new \Exception("The format " . $format . " is not implemented yet");
        }
    }
}