<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TweetRepository")
 */
class Tweet
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

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
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
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
    public function getCreatedAt()
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
}
