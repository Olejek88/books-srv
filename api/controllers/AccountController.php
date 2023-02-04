<?php

namespace api\controllers;

use common\models\User;
use frontend\controllers\UserController;
use Yii;
use yii\rest\ActiveController;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;

/**
 * AccountController class
 */
class AccountController extends ActiveController
{
    public $enableCsrfValidation = false;

    /**
     * Init
     *
     * @return void
     *
     * @throws UnauthorizedHttpException
     */
    public function init()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $token = TokenController::getTokenString(Yii::$app->request);
        // проверяем авторизацию пользователя
        if (!TokenController::isTokenValid($token)) {
            throw new UnauthorizedHttpException();
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
     * @return void
     */
    public function actionIndex()
    {
        $this->redirect('account/me');
    }

    /**
     * Me
     *
     * @return User
     * @throws NotFoundHttpException
     */
    public function actionMe()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $token = TokenController::getTokenString(Yii::$app->request);
        $user = UserController::getUserByToken($token);
        if ($user != null) {
            return $user;
        } else {
            throw new NotFoundHttpException();
        }
    }

    /**
     * Me
     *
     * @throws UnauthorizedHttpException
     */
    public function actionRegister()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
    }
}
