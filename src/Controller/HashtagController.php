<?php
/**
 * Created by PhpStorm.
 * User: rolando.caldas
 * Date: 11/06/2018
 * Time: 18:41
 */

namespace App\Controller;

use App\Repository\HashtagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Hashtag;


class HashtagController extends Controller
{
    /**
     * @Route("/", name="list_hashtags")
     *
     * @param HashtagRepository $hashtagRepository
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    function listHashtags(HashtagRepository $hashtagRepository)
    {
        return $this->render('default/hashtag/listHashtags.html.twig', [
            'hashtags' => $hashtagRepository->findAll(),
        ]);
    }
}