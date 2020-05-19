<?php

namespace App\Entity;

class TweetCollection implements \Iterator
{
    private $position;
    private $entities;

    public function __construct(Tweet ... $tweets)
    {
        $this->entities = $tweets;
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function current()
    {
        return $this->entities[$this->position];
    }

    public function key() : int
    {
        return $this->position;
    }

    public function next()
    {
        ++$this->position;
    }

    public function valid() : bool
    {
        return isset($this->entities[$this->position]);
    }
}
