<?php

namespace App\Entity;

use App\Entity\Traits\Uuidable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TweetRepository")
 */
class Tweet
{

    use Uuidable;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    private $tweetId;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $userName;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $userImage;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $originalTweetUsername;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Hashtag", inversedBy="tweet")
     * @ORM\JoinColumn(nullable=false)
     */
    private $hashtag;

    public function __construct()
    {
        $this->generateId();
    }

    public function __toString()
    {
        return $this->getCreatedAt()->format('Y/m/d H:i:s') . ': @' . $this->getUserName();
    }

    public static function buildAndAttachToHashtag(\StdClass $tweet, Hashtag $hashtag) : self
    {
        $originalTweetUsername = isset($tweet->retweeted_status) ? $tweet->retweeted_status->user->screen_name : null;

        $entity = new self;
        $entity
            ->setTweetId($tweet->id)
            ->setContent($tweet->text)
            ->setUserName($tweet->user->screen_name)
            ->setOriginalTweetUsername($originalTweetUsername)
            ->setUserImage($tweet->user->profile_image_url)
            ->setCreatedAt(new \DateTime($tweet->created_at))
            ->setHashtag($hashtag);

        return $entity;
    }

    /**
     * @return mixed
     */
    public function getTweetId()
    {
        return $this->tweetId;
    }

    /**
     * @param mixed $tweetId
     *
     * @return Tweet
     */
    public function setTweetId($tweetId)
    {
        $this->tweetId = $tweetId;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * @param mixed $userName
     *
     * @return Tweet
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUserImage()
    {
        return $this->userImage;
    }

    /**
     * @param mixed $userImage
     *
     * @return Tweet
     */
    public function setUserImage($userImage)
    {
        $this->userImage = $userImage;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOriginalTweetUsername()
    {
        return $this->originalTweetUsername;
    }

    /**
     * @param mixed $originalTweetUsername
     *
     * @return Tweet
     */
    public function setOriginalTweetUsername($originalTweetUsername)
    {
        $this->originalTweetUsername = $originalTweetUsername;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     *
     * @return Tweet
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt() : \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     *
     * @return Tweet
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getLink()
    {
        return  "<a href='https://twitter.com/{$this->getUserName()}/status/{$this->getTweetId()}' target='_blank'>{$this->getTweetId()}</a>";
    }

    public function getHashtag(): ?Hashtag
    {
        return $this->hashtag;
    }

    public function setHashtag(?Hashtag $hashtag): self
    {
        $this->hashtag = $hashtag;

        return $this;
    }
}
