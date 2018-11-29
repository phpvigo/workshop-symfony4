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

class HashtagController
{
    /**
     * @Route("/", name="list_hashtags")
     *
     * @param HashtagRepository $hashtagRepository
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    function listHashtags(HashtagRepository $hashtagRepository, \Twig_Environment $twig)
    {
        $useCase = new ObtainAllHashtags($hashtagRepository);
        $data = $useCase->dispatch();

        return (new Response())->setContent($twig->render('default/hashtag/listHashtags.html.twig', [
            'hashtags' => $data->hashtags(),
        ]));
    }
}