<?php

namespace api\controllers;

use common\models\Token;
use common\models\User;
use Yii;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotAcceptableHttpException;
use yii\web\Request;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;

class TokenController extends Controller
{
    public $enableCsrfValidation = false;

    /**
     * @param $userLogin
     * @return Token Объект токена.
     */
    public static function getTokenByLogin($userLogin)
    {
        $tokens = Token::find()->where(['userLogin' => $userLogin])->all();
        if (count($tokens) > 1) {
            return null;
        } else if (count($tokens) == 1) {
            return $tokens[0];
        } else {
            return null;
        }
    }

    /**
     * @param string $token
     * @return boolean
     */
    public static function isTokenValid($token)
    {
        if ($token == null) {
            return false;
        }

        return Token::isTokenValid($token);
    }

    /**
     * @param Request $request
     * @return string Токен.
     */
    public static function getTokenString($request)
    {
        if ($request == null) {
            return null;
        }

        $value = $request->getHeaders()->get('Authorization');
        if ($value == null) {
            return null;
        } else {
            $result = explode('bearer ', $value);
            if (count($result) == 2) {
                return $result[1];
            } else {
                return null;
            }
        }
    }

    /**
     * @param string $token Токен.
     * @return User
     */
    public static function getUserByToken($token)
    {
        $tokens = Token::find()->where(['accessToken' => $token])->all();
        if (count($tokens) > 1) {
            return null;
        } else if (count($tokens) == 1) {
            $user = User::find()->where(['id' => $tokens[0]->id])->all();
            return $user[0];
        } else {
            return null;
        }
    }

    /**
     * @param $userId
     * @return User
     */
    public static function getUser($userId)
    {
        $condition = null;
        $users = User::find()->where(['id' => $userId])->all();
        if (count($users) > 1) {
            return null;
        } else if (count($users) == 1) {
            return $users[0];
        } else {
            return null;
        }
    }

    /**
     * @return string Token.
     * @throws NotAcceptableHttpException
     * @throws HttpException
     * @throws UnauthorizedHttpException
     * @throws \Exception
     */
    public function actionIndex()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $request = Yii::$app->request;
        if (!$request->isPost) {
            throw new NotAcceptableHttpException();
        }

        $userLogin = $request->post('userLogin');
        $user = self::getUserByLogin($userLogin);
        if ($user == null) {
            throw new UnauthorizedHttpException();
        }

        $token = Token::findOne(['id' => $user->id]);
        if ($token != null) {
            $start = time();
            $end = $start + 86400;
            $token->accessToken = Token::initToken();
            $token->expiresIn = $end;
            $token->userName = $user->email;
            $token->issued = date('Y-m-d\TH:i:s', $start);
            $token->expires = date('Y-m-d\TH:i:s', $end);
            $token->save();
        } else {
            // создаём токен
            $token = self::createToken(
                $user->email, $user->id
            );

            if ($token == null) {
                throw new HttpException(500, Yii::t('app', 'Ошибка получения токена!'));
            }
        }
        $answer[] = 0;
        $answer["status_code"] = 0;
        $answer["message"] = "";
        $answer["token"] = $token->accessToken;
        return $answer;
    }

    /**
     * @param $userLogin
     * @return User
     */
    public static function getUserByLogin($userLogin)
    {
        $condition = null;
        $users = User::find()->where(['email' => $userLogin])->all();
        if (count($users) > 1) {
            return null;
        } else if (count($users) == 1) {
            return $users[0];
        } else {
            return null;
        }
    }

    /**
     * @param string $login Login пользователя.
     * @param string $id Id метки.
     * @return Token | null
     * @throws \Exception
     */
    public static function createToken($login, $id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if ($login == null || $id == null) {
            return null;
        }

        $start = time();
        $end = $start + 86400;

        $token = new Token();
        $token->id = sprintf("%s", $id);
        $token->accessToken = Token::initToken();
        $token->tokenType = "rest";
        $token->expiresIn = $end;
        $token->userName = $login;
        $token->issued = date('Y-m-d\TH:i:s', $start);
        $token->expires = date('Y-m-d\TH:i:s', $end);

        if (!$token->save()) {
            return null;
        }
        return $token;
    }
}
