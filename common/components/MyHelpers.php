<?php

namespace common\components;

use Yii;

class MyHelpers
{
    /**
     * Возвращает полный путь до файла на сайте.
     *
     * @param string $path Путь справа от корня.
     * @param bool $root От корня.
     *
     * @return string Полный путь.
     */
    public static function getImgUrlPath($path, $root = true)
    {
        $userName = Yii::$app->user->username;

        if ($root) {
            $result = '/';
        } else {
            $result = '';
        }

        return $result . "storage/$userName$path";
    }

    public static function getImgRemotePath($path)
    {
        $localPath = 'storage/' . $path;
        $url = "";
        if (file_exists(Yii::getAlias($localPath))) {
            $userName = Yii::$app->user->username;
            $dir = 'storage/' . $userName . $path;
            $url = Yii::$app->request->BaseUrl . '/' . $dir;
        }
        return $url;
    }

    /**
     * Распознование и форматирование даты
     *
     * @param string $date Дата в виде строки
     * @param string $format Формат выходной даты
     *
     * @return string
     */
    static function parseFormatDate($date, $format = 'Y-m-d H:i:s')
    {
        $time = strtotime($date);
        if ($time <= 0) {
            return date($format);
        } else {
            return date($format, $time);
        }
    }
}

