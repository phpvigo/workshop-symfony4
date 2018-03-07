<?php

namespace App\Controller;

use App\Service\TwitterClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;

class DefaultController extends Controller
{

    /**
     * @Route("/injection")
     */
    public function test_injection(TwitterClient $twitterClient)
    {
        return new Response($twitterClient->sayHello());
    }

    /**
     * @Route("/oauth", name="twitter_oauth")
     */
    public function index()
    {

        $stack = HandlerStack::create();

        // Change oAuth credentials with yours
        $middleware = new Oauth1([
            'consumer_key' => 'my_key',
            'consumer_secret' => 'my_secret',
            'token' => 'my_token',
            'token_secret' => 'my_token_secret'
        ]);

        $stack->push($middleware);

        $client = new Client([
            'base_uri' => 'https://api.twitter.com/1.1/',
            'handler' => $stack
        ]);

        // Set the "auth" request option to "oauth" to sign using oauth

        $res = $client->get('search/tweets.json', [
            'auth' => 'oauth',
            'query' => [
                'q' => urlencode('#fariña'),
                'include_entities' => false,
                'result_type' => 'recent',
                'count' => 4
            ]
        ]);

        return $this->render('default/index.html.twig', [
            'response' => json_decode($res->getBody()->getContents())
        ]);

    }
}
