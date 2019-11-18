<?php


namespace App\UseCase;

use App\Entity\HashtagRepository;
use App\Entity\TweetRepository;
use App\Service\TweetTransformChain\TweetTransformChainFactory;
use App\Service\TweetTransformChain\TweetTransformRequest;

class TransformTweetsOfHashtagIntoFormat
{
    private $hashtagRepository;
    private $tweetRepository;
    private $tweetTransformChainFactory;

    public function __construct(HashtagRepository $hashtagRepository, TweetRepository $tweetRepository, TweetTransformChainFactory $tweetTransformChainFactory)
    {
        $this->hashtagRepository = $hashtagRepository;
        $this->tweetRepository = $tweetRepository;
        $this->tweetTransformChainFactory = $tweetTransformChainFactory;
    }

    public function dispatch(string $slug, string $type) : string
    {
        $hashtag = $this->hashtagRepository->bySlugOrFail($slug);
        $tweets = $this->tweetRepository->allByHashtag($hashtag);

        $tweetTransformChain = $this->tweetTransformChainFactory->chain();

        return $tweetTransformChain->handle(new TweetTransformRequest($type, $hashtag, $tweets));
    }
}
