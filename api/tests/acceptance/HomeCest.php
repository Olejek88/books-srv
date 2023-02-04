<?php

namespace frontend\tests\acceptance;

use frontend\tests\AcceptanceTester;
use yii\helpers\Url;

class HomeCest
{
    public function checkHome(AcceptanceTester $I)
    {
        $I->amOnPage(Url::toRoute('/'));
        $I->see('С помощью Toir API вы можете использовать функциональные возможности Toir для своего сайта или приложения.');
        $I->seeLink('Вход');
        $I->see('Регистрация приложения');
    }
}
