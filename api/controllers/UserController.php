<?php

namespace api\controllers;

use common\models\User;
use Yii;
use yii\base\Controller;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;

/**
 * @package api\controllers
 */
class UserController extends Controller
{
    public $modelClass = 'common\models\User';

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
     * Displays homepage.
     *
     * @return array
     */
    public function actionIndex()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $req = Yii::$app->request;
        $query = User::find();

        $email = $req->getQueryParam('email');
        if ($email != null) {
            $query->andWhere(['email' => $email]);
        }

        if ($query->where == null) {
            return [];
        }

        $result = $query->all();
        return $result;
    }
}
