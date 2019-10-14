<?php

namespace App\Entity;

interface TweetRepository
{
    public function allByHashtag(Hashtag $hashtag) : TweetCollection;
}