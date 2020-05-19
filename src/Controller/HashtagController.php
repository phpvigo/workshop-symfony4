<?php
/**
 * Created by PhpStorm.
 * User: rolando.caldas
 * Date: 11/06/2018
 * Time: 18:41
 */

namespace App\Controller;

use App\Repository\HashtagRepository;
use App\UseCase\ObtainAllHashtags;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class HashtagController
{
    /**
     * @Route("/", name="list_hashtags")
     *
     * @param HashtagRepository $hashtagRepository
     * @param Environment $twig
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function listHashtags(HashtagRepository $hashtagRepository, Environment $twig): Response
    {
        $useCase = new ObtainAllHashtags($hashtagRepository);
        $data = $useCase->dispatch();

        return (new Response())->setContent($twig->render('default/hashtag/listHashtags.html.twig', [
            'hashtags' => $data->hashtags(),
        ]));
    }
}
