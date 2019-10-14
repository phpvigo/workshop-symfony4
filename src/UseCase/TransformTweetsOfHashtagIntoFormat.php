<?php


namespace App\UseCase;


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class TransformTweetsOfHashtagIntoFormat
{
    public function dispatch($hashtag, $tweets, $type) : string
    {
        $return = null;
        switch ($type) {
            case "json":
                $return = $this->responseJson($tweets);
                break;

            case "csv":
                $return = $this->responseCsv($tweets);
                break;

            case "excel":
                $return = $this->responseExcel($tweets, $hashtag);
                break;
        }
        return $return;
    }

    /**
     * @param array $tweets
     * @return string
     */
    private function responseJson(array $tweets): string
    {
        $data = [];
        foreach ($tweets AS $tweet) {
            $data[] = [
                'id' => $tweet->getTweetId(),
                'username' => $tweet->getUserName(),
                'user_image' => $tweet->getUserImage(),
                'content' => $tweet->getContent(),
                'link' => 'https://twitter.com/' . $tweet->getUserName() . '/status/' . $tweet->getTweetId(),
                'date' => $tweet->getCreatedAt()->format('c')
            ];
        }

        return json_encode($data);
    }

    /**
     * @param array $tweets
     * @return string
     */
    private function responseCsv(array $tweets): string
    {
        $csvTempFile = fopen('php://memory', 'r+');
        fputcsv($csvTempFile, ['id', 'username', 'user_image', 'content', 'link', 'date']);
        foreach ($tweets AS $tweet) {
            fputcsv($csvTempFile, [$tweet->getTweetId(), $tweet->getUserName(), $tweet->getUserImage(), $tweet->getContent(), 'https://twitter.com/' . $tweet->getUserName() . '/status/' . $tweet->getTweetId(), $tweet->getCreatedAt()->format('c')]);
        }
        rewind($csvTempFile);
        $csv_line = stream_get_contents($csvTempFile);
        fclose($csvTempFile);
        return $csv_line;
    }

    /**
     * @param array $tweets
     * @param \App\Entity\Hashtag|null $hashtag
     * @return string
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    private function responseExcel(array $tweets, ?\App\Entity\Hashtag $hashtag): string
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
        foreach ($tweets AS $tweet) {
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