<?php


namespace App\Service\TweetTransformChain;

abstract class AbstractTweetTransform implements TweetTransformHandler
{
    /**
     * @var TweetTransformHandler
     */
    private $nextHandler;

    public function setNext(TweetTransformHandler $handler): void
    {
        $this->nextHandler = $handler;
    }

    public function handle(TweetTransformRequest $tweetTransformRequest): string
    {
        return $this->nextHandler ? $this->nextHandler->handle($tweetTransformRequest) : '';
    }


}