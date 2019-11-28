<?php


namespace App\Service\TweetTransformChain;


interface TweetTransformHandler
{
    public function setNext(TweetTransformHandler $handler) : void;
    public function handle(TweetTransformRequest $tweetTransformRequest) : string;
}