<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use App\Entity\Traits\Uuidable;
use App\Controller\RandomTweetForHashtagAction;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use App\Controller\TweetFilteredByStringAction;

/**
 * @ApiResource(
 *      security="is_granted('ROLE_USER')",
 *      itemOperations={
 *          "get"={
 *               "requirements"={"id"="^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$"}
 *          },
 *          "get-random-tweet-from-hashtag"={
 *              "path"="/tweets/hashtag/{hashtagId}/random-tweet",
 *              "method"="GET",
 *              "controller"=RandomTweetForHashtagAction::class,
 *              "openapi_context"={
 *                  "summary"="Get random tweet from a tweet hashtag",
 *                  "parameters"={
 *                      {
 *                          "name"="hashtagId",
 *                          "description"="UUID for hashtag",
 *                          "in"="path",
 *                          "schema"={"type"="uuid"},
 *                          "required"=true
 *                      },
 *                  }
 *               },
 *              "read"=false
 *          }
 *      },
 *     collectionOperations={
 *          "get",
 *          "get-filtered-by-string"={
 *              "security"="is_granted('ROLE_ADMIN')",
 *              "path"="tweets/global-search",
 *              "method"="GET",
 *              "openapi_context"={
 *                  "summary"="Global search for tweets (search over fields username, hashtagName, and content)",
 *                  "description"="Search over fields username, hashtagName, and content",
 *                  "parameters"={
 *                      {
 *                          "name"="search",
 *                          "description"="Seach string",
 *                          "in"="query",
 *                          "schema"={"type"="string"},
 *                          "required"=false
 *                      },
 *                  }
 *               },
 *          }
 *      }
 * )
 * @ApiFilter(SearchFilter::class, properties={
 *          "id":"exact",
 *          "tweetId":"exact",
 *          "userName":"partial",
 *          "originalTweetUsername":"partial",
 *          "content":"partial",
 *          "hashtag.name": "exact"
 *      })
 * @ApiFilter(OrderFilter::class, properties={"id", "userName", "hashtag.name", "createdAt"}, arguments={"orderParameterName"="order"})
 * @ApiFilter(DateFilter::class, properties={"createdAt"})
 */
class Tweet
{
    use Uuidable;

    private $tweetId;
    private $userName;
    private $userImage;
    private $originalTweetUsername;
    private $content;
    private $createdAt;
    private $hashtag;
    private $hashtagName;

    public function __construct()
    {
        $this->generateId();
    }

    public function __toString()
    {
        return $this->getCreatedAt()->format('Y/m/d H:i:s').': @'.$this->getUserName();
    }

    /**
     * @param \StdClass $tweet
     * @param Hashtag $hashtag
     * @return Tweet
     */
    public static function buildAndAttachToHashtag(\StdClass $tweet, Hashtag $hashtag): self
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
    public function getTweetId(): int
    {
        return $this->tweetId;
    }

    /**
     * @param int $tweetId
     *
     * @return Tweet
     */
    public function setTweetId(int $tweetId): self
    {
        $this->tweetId = $tweetId;

        return $this;
    }

    /**
     * @return string
     */
    public function getUserName(): string
    {
        return $this->userName;
    }

    /**
     * @param mixed $userName
     *
     * @return Tweet
     */
    public function setUserName(string $userName): self
    {
        $this->userName = $userName;

        return $this;
    }

    /**
     * @return string
     */
    public function getUserImage(): string
    {
        return $this->userImage;
    }

    /**
     * @param string $userImage
     *
     * @return Tweet
     */
    public function setUserImage(string $userImage): self
    {
        $this->userImage = $userImage;

        return $this;
    }

    /**
     * @return string
     */
    public function getOriginalTweetUsername(): ?string
    {
        return $this->originalTweetUsername;
    }

    /**
     * @param string $originalTweetUsername
     *
     * @return Tweet
     */
    public function setOriginalTweetUsername(?string $originalTweetUsername): self
    {
        $this->originalTweetUsername = $originalTweetUsername;

        return $this;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     *
     * @return Tweet
     */
    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return Tweet
     */
    public function setCreatedAt($createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return "<a href='https://twitter.com/{$this->getUserName()}/status/{$this->getTweetId()}' target='_blank'>{$this->getTweetId()}</a>";
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

    public function getHashtagName()
    {
        return $this->getHashtag()->getName();
    }
}
