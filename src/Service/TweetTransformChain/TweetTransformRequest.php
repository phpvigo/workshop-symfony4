<?php

namespace App\Service\TweetTransformChain;

use App\Entity\Hashtag;
use App\Entity\TweetCollection;

class TweetTransformRequest
{
    /**
     * @var string
     */
    private $type;
    /**
     * @var Hashtag
     */
    private $hashtag;
    /**
     * @var TweetCollection
     */
    private $tweetCollection;

    public function __construct(string $type, Hashtag $hashtag, TweetCollection $tweetCollection)
    {
        $this->type = $type;
        $this->hashtag = $hashtag;
        $this->tweetCollection = $tweetCollection;
    }

    /**
     * @return string
     */
    public function type(): string
    {
        return $this->type;
    }

    /**
     * @return Hashtag
     */
    public function hashtag(): Hashtag
    {
        return $this->hashtag;
    }

    /**
     * @return TweetCollection
     */
    public function tweetCollection(): TweetCollection
    {
        return $this->tweetCollection;
    }
}
