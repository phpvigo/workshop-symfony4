<?php


namespace App\Service\TweetTransformChain;


class TweetTransformChainFactory
{
    /**
     * @var TweetTransformHandler
     */
    private $chain;

    public function __construct(TweetTransformHandler ... $tweetTransformHandlers)
    {
        $lastHandler = null;
        foreach ($tweetTransformHandlers AS $tweetTransformHandler) {
            $this->setNextIfChainNotExists($tweetTransformHandler, $lastHandler);
            $this->setChainIfNotExists($tweetTransformHandler);
            $lastHandler = $tweetTransformHandler;
        }
    }

    private function setNextIfChainNotExists(TweetTransformHandler $tweetTransformHandler, ?TweetTransformHandler $lastHandler) : void
    {
        if ($lastHandler === null) {
            return;
        }

        $lastHandler->setNext($tweetTransformHandler);
    }

    private function setChainIfNotExists(TweetTransformHandler $tweetTransformHandler) : void
    {
        if ($this->chain !== null) {
            return;
        }
        $this->chain = $tweetTransformHandler;
    }

    public function chain() : ?TweetTransformHandler
    {
        return $this->chain;
    }
}