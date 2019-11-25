<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ApiPlatform\Core\Exception\ResourceClassNotSupportedException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Entity\Tweet;

final class TweetFilteredByStringDataProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface
{
    private $requestStack;
    private $entityManager;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === Tweet::class && $operationName === 'get-filtered-by-string';
    }

    public function getCollection(string $resourceClass, string $operationName = null)
    {
        $searchString = $this->requestStack->getCurrentRequest()->get('search');

        return $this->entityManager->getRepository(Tweet::class)->createQueryBuilder('t')
            ->join('t.hashtag', 'h')
            ->where('t.userName LIKE :searchString')
            ->orWhere('t.content LIKE :searchString')
            ->orWhere('h.name LIKE :searchString')
            ->setParameter('searchString', '%'.$searchString.'%')
            ->getQuery()
            ->getResult();
    }
}
