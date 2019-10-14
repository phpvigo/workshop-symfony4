<?php

namespace App\Controller;

use App\Service\ResponserFormatterContainer;
use App\UseCase\TransformTweetsOfHashtagIntoFormat;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExportController
{
    /**
     * @Route("/export/{slug}/{type}", name="export_hashtag")
     *
     * @param string $slug
     * @param string $type
     * @param TransformTweetsOfHashtagIntoFormat $transformTweetsOfHashtagIntoFormat
     * @param ResponserFormatterContainer $responserFormatterContainer
     * @return Response
     */
    public function exportHashtag(
        string $slug,
        string $type,
        TransformTweetsOfHashtagIntoFormat $transformTweetsOfHashtagIntoFormat,
        ResponserFormatterContainer $responserFormatterContainer
    ) {
        return $responserFormatterContainer->format(
            $type,
            $transformTweetsOfHashtagIntoFormat->dispatch($slug, $type)
        );
    }
}
