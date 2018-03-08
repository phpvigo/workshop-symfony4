<?php

namespace App\Service;

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
     * @param $text
     * @param bool   $include_entities
     * @param string $result_type
     * @param int    $count
     *
     * @return array
     */
    public function findTweetsWith(
        $text,
        $include_entities = false,
        $result_type = 'recent',
        $count = 4
    ) {
        try {
            $JSONResponse = $this->get('search/tweets.json', [
                'auth' => 'oauth',
                'query' => [
                    'q' => $text,
                    'include_entities' => $include_entities,
                    'result_type' => $result_type,
                    'count' => $count,
                ],
            ])->getBody()->getContents();

            return json_decode($JSONResponse);
        } catch (\Exception $e) {
            return false;
        }
    }
}
