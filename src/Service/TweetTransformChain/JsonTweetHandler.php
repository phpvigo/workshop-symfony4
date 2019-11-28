<?php


namespace App\Service\TweetTransformChain;

use App\Entity\TweetCollection;

class JsonTweetHandler extends AbstractTweetTransform
{
    public function handle(TweetTransformRequest $tweetTransformRequest): string
    {
        if ($tweetTransformRequest->type() !== 'json') {
            return parent::handle($tweetTransformRequest);
        }

        return $this->processResponse($tweetTransformRequest->tweetCollection());
    }

    /**
     * @param TweetCollection $tweets
     * @return string
     */
    private function processResponse(TweetCollection $tweets): string
    {
        $data = [];
        foreach ($tweets as $tweet) {
            $data[] = [
                'id' => $tweet->getTweetId(),
                'username' => $tweet->getUserName(),
                'user_image' => $tweet->getUserImage(),
                'content' => $tweet->getContent(),
                'link' => 'https://twitter.com/' . $tweet->getUserName() . '/status/' . $tweet->getTweetId(),
                'date' => $tweet->getCreatedAt()->format('c')
            ];
        }

        return json_encode($data);
    }
}
