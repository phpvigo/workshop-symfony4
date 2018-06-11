<?php

namespace App\Service;

use App\Entity\Hashtag;
use GuzzleHttp\Client;

class TwitterClient extends Client
{
    public function __construct(array $config = [])
    {
        parent::__construct($config);
    }

    /**
     * Find tweets by text.
     *
     * @param Hashtag $hashtag
     * @param bool $include_entities
     * @param string $result_type
     * @param int $count
     *
     * @return array
     */
    public function findTweetsWith(
        Hashtag $hashtag,
        $include_entities = false,
        $result_type = 'recent',
        $count = 4
    ) {
        try {

            $options = [
                'auth' => 'oauth',
                'query' => [
                    'q' => $hashtag->getName(),
                    'include_entities' => $include_entities,
                    'result_type' => $result_type,
                    'count' => $count,
                    'since_id' => $hashtag->getLastTweet()
                ]
            ];

            $JSONResponse = $this->get('search/tweets.json', $options)->getBody()->getContents();

            return json_decode($JSONResponse);
        } catch (\Exception $e) {
            return false;
        }
    }
}
