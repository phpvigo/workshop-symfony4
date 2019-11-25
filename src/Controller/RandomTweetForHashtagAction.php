<?php

namespace App\Controller;

use App\Entity\Hashtag;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Validation;
use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;

class RandomTweetForHashtagAction
{
    private $requestStack;
    private $entityManager;

    public function __construct(
        RequestStack $requestStack,
        EntityManagerInterface $entityManager
    ) {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
    }

    public function __invoke()
    {
        $hashTagId = $this->requestStack->getCurrentRequest()->get('hashtagId');

        $validation = Validation::createValidator();
        $violations = $validation->validate(
            $hashTagId,
            [
                new \Symfony\Component\Validator\Constraints\Uuid(),
            ]
        );

        if (0 !== \count($violations)) {
            throw new ValidationException($violations);
        }

        $hashTag = $this->entityManager->getRepository(Hashtag::class)->findOneById(Uuid::fromString($hashTagId));

        if (!$hashTag) {
            return new JsonResponse(null, 404);
        }

        $tweetCountForCurrentHashtag = $hashTag->getTweet()->count();

        if ($tweetCountForCurrentHashtag > 0) {
            $randomIndex = rand(0, $tweetCountForCurrentHashtag - 1);

            return $hashTag->getTweet()[$randomIndex];
        }

        return new JsonResponse(null, 404);
    }
}
