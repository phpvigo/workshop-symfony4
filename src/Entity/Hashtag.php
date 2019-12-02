<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use App\Entity\Traits\Uuidable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;

/**
 * @ApiResource(
 *     security="is_granted('ROLE_USER')",
 *     itemOperations={"get"},
 *     collectionOperations={"get"}
 * )
 * @ApiFilter(SearchFilter::class, properties={"id":"exact", "name":"partial"})
 * @ApiFilter(OrderFilter::class, properties={"id", "lastTweet"}, arguments={"orderParameterName"="order"})
 */
class Hashtag
{
    use Uuidable;

    private $name;
    private $tweet;
    private $lastTweet;

    public function __construct()
    {
        $this->generateId();
        $this->name = '';
        $this->tweet = new ArrayCollection();
    }

    public static function fromName(string $string) : self
    {
        $hashtag = new self();
        $hashtag->setName($string);
        return $hashtag;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        if (!empty($this->name)) {
            throw new \Exception("The Hashtag can't be changed");
        }

        $this->name = $name;
        return $this;
    }

    /**
     * @return Collection|Tweet[]
     */
    public function getTweet(): Collection
    {
        return $this->tweet;
    }

    public function addTweet(Tweet $name): self
    {
        if (!$this->tweet->contains($name)) {
            $this->tweet[] = $name;
            $name->setHashtag($this);
        }

        return $this;
    }

    public function removeTweet(Tweet $name): self
    {
        if ($this->tweet->contains($name)) {
            $this->tweet->removeElement($name);
            // set the owning side to null (unless already changed)
            if ($name->getHashtag() === $this) {
                $name->setHashtag(null);
            }
        }

        return $this;
    }

    public function getLastTweet(): ?int
    {
        return $this->lastTweet;
    }

    public function setLastTweet(?int $lastTweet): self
    {
        $this->lastTweet = $lastTweet;

        return $this;
    }

    public function __toString()
    {
        return (string) $this->name;
    }
}
