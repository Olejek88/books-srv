<?php

namespace api\controllers;

use api\models\CorsCustom;
use common\models\Category;
use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\ContentNegotiator;
use yii\rest\ActiveController;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;

class CategoryController extends ActiveController
{
    public $modelClass = 'common\models\Category';

    public $enableCsrfValidation = false;

    /**
     * @return array
     */
    public function behaviors()
    {
        $_verbs = ['GET', 'POST', 'PATCH', 'PUT', 'DELETE', 'OPTIONS'];
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::className(),
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],

        ];
        // remove authentication filter
        $auth = $behaviors['authenticator'];
        unset($behaviors['authenticator']);
        // add CORS filter
        $behaviors['corsFilter'] = [
            'class' => CorsCustom::className(),
        ];
        // re-add authentication filter
        $behaviors['authenticator'] = $auth;
        // avoid authentication on CORS-pre-flight requests (HTTP OPTIONS method)
        $behaviors['authenticator']['except'] = ['options'];
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
            'except' => ['login']
        ];

        /*        $behaviors['corsFilter'] = [
                    'class' => Cors::className(),
                    'cors' => [
                        'Origin' => ['*'],
                        'Access-Control-Request-Method' => $_verbs,
                        'Access-Control-Allow-Headers' => ['content-type'],
                        'Access-Control-Request-Headers' => ['*'],
                    ]];*/
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
        // ?????????????????? ?????????????????????? ????????????????????????
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
