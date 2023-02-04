<?php

namespace api\controllers;

use api\models\SignupForm;
use common\models\LoginForm;
use Yii;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\rest\Controller;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['login', 'register'],
                'rules' => [
                    [
                        'actions' => ['register'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['login'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                    'login' => ['post'],
                    'register' => ['post']
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     * @throws InvalidConfigException
     */
    public function actionLogin()
    {
        $model = new LoginForm();
        $request = Yii::$app->request->getBodyParams();
        $model->email = $request["email"];
        $model->password = $request["password"];
        if ($user = $model->login()) {
            $answer[] = 0;
            $answer["status_code"] = 0;
            $answer["user_id"] = $user->id;
            $answer["message"] = "user successfully login";
            $answer["email"] = $user->email;
            $answer["user_name"] = $user->username;
            $answer["access_token"] = $user->verification_token;
            return json_encode($answer);
        }
        $answer["status_code"] = -1;
        $answer["user_id"] = -1;
        $answer["message"] = json_encode($model->errors);
        $answer["email"] = "";
        $answer["access_token"] = null;
        return json_encode($answer);
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        $answer["status_code"] = 0;
        $answer["message"] = "user successfully log out";
        return json_encode($answer);
    }

    /**
     * Signs user up.
     *
     * @return mixed
     * @throws Exception
     */
    public function actionRegister()
    {
        $model = new SignupForm();
        $request = Yii::$app->request->getBodyParams();
        $model->username = $request["username"];
        $model->password = $request["password"];
        $model->email = $request["email"];
        if ($user = $model->signup()) {
            if (Yii::$app->getUser()->login($user)) {
                $answer[] = 0;
                $answer["status_code"] = 0;
                $answer["user_id"] = $user->id;
                $answer["message"] = "user successfully created";
                $answer["email"] = $model->email;
                $answer["user_name"] = $model->username;
                $answer["access_token"] = $user->verification_token;
                return json_encode($answer);
            }
        }
        $answer[] = 0;
        $answer["status_code"] = -1;
        $answer["user_id"] = -1;
        $answer["message"] = json_encode($model->errors);
        $answer["email"] = "";
        $answer["user_name"] = $model->username;
        $answer["access_token"] = null;
        return json_encode($answer);
    }
}
