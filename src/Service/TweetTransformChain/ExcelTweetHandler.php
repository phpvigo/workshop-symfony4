<?php

namespace App\Service\TweetTransformChain;

use App\Entity\TweetCollection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelTweetHandler extends AbstractTweetTransform
{
    public function handle(TweetTransformRequest $tweetTransformRequest): string
    {
        if ($tweetTransformRequest->type() !== 'excel') {
            return parent::handle($tweetTransformRequest);
        }

        return $this->processResponse($tweetTransformRequest->tweetCollection());
    }

    /**
     * @param TweetCollection $tweets
     * @return string
     */
    private function processResponse(TweetCollection $tweets): string
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);
        $worksheet = new Worksheet();
        $spreadsheet->addSheet($worksheet);
        $i = 1;
        $worksheet->setCellValue('A' . $i, 'id');
        $worksheet->setCellValue('B' . $i, 'username');
        $worksheet->setCellValue('C' . $i, 'user_image');
        $worksheet->setCellValue('D' . $i, 'content');
        $worksheet->setCellValue('E' . $i, 'link');
        $worksheet->setCellValue('F' . $i, 'date');
        $i++;
        foreach ($tweets as $tweet) {
            $worksheet->setCellValue('A' . $i, $tweet->getTweetId());
            $worksheet->setCellValue('B' . $i, $tweet->getUserName());
            $worksheet->setCellValue('C' . $i, $tweet->getUserImage());
            $worksheet->setCellValue('D' . $i, $tweet->getContent());
            $worksheet->setCellValue('E' . $i, 'https://twitter.com/' . $tweet->getUserName() . '/status/' . $tweet->getTweetId());
            $worksheet->setCellValue('F' . $i, $tweet->getCreatedAt()->format('c'));

            $worksheet->getColumnDimension('A')->setAutoSize(true);
            $worksheet->getColumnDimension('B')->setAutoSize(true);
            $worksheet->getColumnDimension('C')->setAutoSize(true);
            $worksheet->getColumnDimension('D')->setAutoSize(true);
            $worksheet->getColumnDimension('E')->setAutoSize(true);
            $worksheet->getColumnDimension('F')->setAutoSize(true);
            $i++;
        }

        ob_start();
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        $content = ob_get_clean();

        return $content;
    }
}
