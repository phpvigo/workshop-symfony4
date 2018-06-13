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
     * @ORM\Column(type="integer", unique=true)
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

    /**
     * @param \StdClass $tweet
     * @param Hashtag $hashtag
     * @return Tweet
     */
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
     * @return int
     */
    public function getTweetId() : int
    {
        return $this->tweetId;
    }

    /**
     * @param int $tweetId
     *
     * @return Tweet
     */
    public function setTweetId(int $tweetId) : self
    {
        $this->tweetId = $tweetId;

        return $this;
    }

    /**
     * @return string
     */
    public function getUserName() : string
    {
        return $this->userName;
    }

    /**
     * @param mixed $userName
     *
     * @return Tweet
     */
    public function setUserName(string $userName) : self
    {
        $this->userName = $userName;

        return $this;
    }

    /**
     * @return string
     */
    public function getUserImage() : string
    {
        return $this->userImage;
    }

    /**
     * @param string $userImage
     *
     * @return Tweet
     */
    public function setUserImage(string $userImage) : self
    {
        $this->userImage = $userImage;

        return $this;
    }

    /**
     * @return string
     */
    public function getOriginalTweetUsername() : ?string
    {
        return $this->originalTweetUsername;
    }

    /**
     * @param string $originalTweetUsername
     *
     * @return Tweet
     */
    public function setOriginalTweetUsername(?string $originalTweetUsername) : self
    {
        $this->originalTweetUsername = $originalTweetUsername;

        return $this;
    }

    /**
     * @return string
     */
    public function getContent() : string
    {
        return $this->content;
    }

    /**
     * @param string $content
     *
     * @return Tweet
     */
    public function setContent(string $content) : self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt() : \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return Tweet
     */
    public function setCreatedAt($createdAt) : self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return string
     */
    public function getLink() : string
    {
        return  "<a href='https://twitter.com/{$this->getUserName()}/status/{$this->getTweetId()}' target='_blank'>{$this->getTweetId()}</a>";
    }

    /**
     * @return Hashtag|null
     */
    public function getHashtag(): ?Hashtag
    {
        return $this->hashtag;
    }

    /**
     * @param Hashtag|null $hashtag
     * @return Tweet
     */
    public function setHashtag(?Hashtag $hashtag): self
    {
        $this->hashtag = $hashtag;

        return $this;
    }
}
