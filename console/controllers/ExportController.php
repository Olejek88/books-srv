<?php

namespace console\controllers;

use common\components\MainFunctions;
use common\models\Book;
use yii\console\Controller;

/**
 * Class ExportController
 * @package console\controllers
 */
class ExportController extends Controller
{
    const LOG_ID = 'export';


    public function actionAdd()
    {
        $csvFile = '1.csv';
        $lines = [];
        $file_to_read = fopen($csvFile, 'r');
        while (!feof($file_to_read)) {
            $lines[] = fgetcsv($file_to_read, 1000, ',');
        }
        fclose($file_to_read);
        //echo '<pre>';
        //print_r($lines);
        //echo '</pre>';
        for ($index = 1; $index < count($lines); $index++) {
            //for ($index = 1; $index < 2; $index++) {
            $link = $lines[$index][12];
            $title = $lines[$index][18];
            $img = $lines[$index][19];
            if ($this->getBookByName($title) == null) {
                echo "[$index]" . $lines[$index][12] . " " . $lines[$index][18] . " " . $lines[$index][19] . PHP_EOL;
                if (!stristr($title, "Gesponserte Anzeige")) {
                    $description = $this->getCompletion($title);
                    echo "store" . PHP_EOL;
                    $this->storeBook($title, $description, $img, $link, "52e522ac-02b4-4b74-98a8-83b29c08b30f");
                }
            }
        }
    }

    public function getBookByName($bookName)
    {
        $bookModel = Book::find()->where(['title' => $bookName])->one();
        if ($bookModel) {
            return $bookModel;
        }
        return null;
    }

    public function getCompletion($bookName)
    {
        $getOpenAITemperature = 0.5;
        $maxTokens = 500;
        $gettop_p = 1;
        $getOpenAIModel = "text-davinci-003";
        $getRequest = "Erzähl mir bitte die Geschichte des Buches: $bookName";
        $ch = curl_init();
        $headers = [
            'Accept: application/json',
            'Content-Type: application/json',
            'Authorization: Bearer ' . $OPENAI_API_KEY . ''
        ];
        $postData = [
            'model' => $getOpenAIModel,
            'prompt' => $getRequest,
            'temperature' => $getOpenAITemperature,
            'max_tokens' => $maxTokens,
            'top_p' => $gettop_p,
            'best_of' => 2,
            'frequency_penalty' => 0.0,
            'presence_penalty' => 0.0,
            'stop' => '["\n"]',
        ];

        curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/completions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));

        $result = curl_exec($ch);
        $response_obj = json_decode($result);
        foreach ($response_obj->choices as $completion) {
            $completion->text;
            //echo $completion->text;
            return $completion->text;
        }
        return null;
    }

    public function storeBook($title, $description, $img, $link)
    {
        $book = new Book();
        try {
            //&linkCode=li2&tag=shtrmvk89-21&ref_=as_li_ss_i
            $book->uuid = MainFunctions::GUID();
            $book->authorName = "Brandon Sanderson";
            $book->authorUuid = "4fd19231-b9eb-4046-9b41-2103d883514b";
            $book->description = $description;
            $book->title = $title;
            $book->imageUrl = $img;
            $book->categoryUuid = "c5515e76-41be-4ac3-bb02-1d34e4f7fc10";
            $book->link = "$link?linkCode=li2&tag=shtrmvk89-21&ref_=as_li_ss_i";
            $book->save(false);
        } catch (\Exception $e) {
            echo "Error";
            echo $e;
        }
    }
}