<?php


namespace App\Service;


use GuzzleHttp\Subscriber\Oauth\Oauth1;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Client;

class TwitterClient
{

    private $client;

    /**
     * TwitterClient constructor.
     *
     * @param $base_uri
     * @param $consumer_key
     * @param $consumer_secret
     * @param $token
     * @param $token_secret
     */
    public function __construct($base_uri, $consumer_key, $consumer_secret, $token, $token_secret)
    {

        $stack = HandlerStack::create();

        $middleware = new Oauth1([
            'consumer_key' => $consumer_key,
            'consumer_secret' => $consumer_secret,
            'token' => $token,
            'token_secret' => $token_secret
        ]);
        $stack->push($middleware);


        $this->client = new Client([
            'base_uri' => $base_uri,
            'handler' => $stack
        ]);
    }

    /**
     * Find tweets by text
     *
     * @param $text
     * @param bool $include_entities
     * @param string $result_type
     * @param int $count
     * @return array
     */
    public function findTweetsWith(
        $text,
        $include_entities = false,
        $result_type = 'recent',
        $count = 4
    )
    {

        try {
            $JSONResponse = $this->client->get('search/tweets.json', [
                'auth' => 'oauth',
                'query' => [
                    'q' => $text,
                    'include_entities' => $include_entities,
                    'result_type' => $result_type,
                    'count' => $count
                ]
            ])->getBody()->getContents();

            return json_decode($JSONResponse);

        } catch (\Exception $e) {
            echo $e->getMessage();
        }

    }

}