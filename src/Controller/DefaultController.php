<?php

namespace App\Controller;

use App\Service\TwitterClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{

    /**
     * @Route("/service")
     * @param TwitterClient $twitterClient
     * @return Response
     */
    public function test_injection(TwitterClient $twitterClient)
    {

        $twitterResult = $twitterClient->findTweetsWith('#farina');

        dump($twitterResult);

        return $this->render('default/index.html.twig', [
            'response' => $twitterResult
        ]);

    }

}
