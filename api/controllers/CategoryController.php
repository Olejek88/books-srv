<?php

namespace api\controllers;

use common\models\Category;
use Yii;
use yii\filters\ContentNegotiator;
use yii\filters\Cors;
use yii\rest\ActiveController;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;

class CategoryController extends ActiveController
{
    public $modelClass = 'common\models\Category';

    /**
     * @return array
     */
    public function behaviors()
    {
        $_verbs = ['GET', 'POST', 'PATCH', 'PUT', 'DELETE'];
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::class,
            'formats' => [
                'application/json' => Response::FORMAT_JSON
            ]
        ];
        $behaviors['corsFilter'] = [
            'class' => Cors::className(),
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Request-Method' => $_verbs,
                'Access-Control-Allow-Headers' => ['content-type'],
                'Access-Control-Request-Headers' => ['*'],
            ]];
        return $behaviors;
    }

    /**
     * Init
     *
     * @return void
     * @throws UnauthorizedHttpException
     */
    public function init()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $token = TokenController::getTokenString(Yii::$app->request);
        // проверяем авторизацию пользователя
        if (!TokenController::isTokenValid($token)) {
            //throw new UnauthorizedHttpException();
        }
    }

    /**
     * Actions
     *
     * @return array
     */
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    /**
     * Index
     *
     * @return Category[]
     */
    public function actionIndex()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $query = Category::find();
        $result = $query->all();
        return $result;
    }

}
