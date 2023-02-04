<?php

namespace api\components;

use common\components\IPhoto;
use common\models\Alarm;
use common\models\AlarmStatus;
use common\models\AlarmType;
use common\models\Book;
use common\models\Category;
use common\models\City;
use common\models\Continent;
use common\models\Contragent;
use common\models\ContragentType;
use common\models\Country;
use common\models\Defect;
use common\models\DefectType;
use common\models\Documentation;
use common\models\DocumentationType;
use common\models\Equipment;
use common\models\EquipmentStatus;
use common\models\EquipmentSystem;
use common\models\EquipmentType;
use common\models\House;
use common\models\HouseStatus;
use common\models\HouseType;
use common\models\MeasureType;
use common\models\Objects;
use common\models\ObjectStatus;
use common\models\ObjectType;
use common\models\Operation;
use common\models\OperationTemplate;
use common\models\RequestStatus;
use common\models\RequestType;
use common\models\Street;
use common\models\TaskTemplate;
use common\models\TaskType;
use common\models\TaskVerdict;
use common\models\UserHouse;
use common\models\UserSystem;
use common\models\WorkStatus;
use Exception;
use Yii;
use yii\db\ActiveRecord;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\ActiveController;
use yii\web\BadRequestHttpException;
use yii\web\NotAcceptableHttpException;

class BaseController extends ActiveController
{
    public $modelClass = ActiveRecord::class;
    public $enableCsrfValidation = false;

    /**
     * @inheritdoc
     */
    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs['create'] = ['POST'];
        $verbs['index'] = ['GET'];
        return $verbs;
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['class'] = HttpBearerAuth::class;
        return $behaviors;
    }

    public function actionIndex()
    {
        $req = Yii::$app->request;

        /** @var ActiveRecord $class */
        $class = $this->modelClass;
        $query = $class::find();

        // проверяем параметры запроса
        $uuid = $req->getQueryParam('uuid');
        if ($uuid != null) {
            $query->andWhere(['uuid' => $uuid]);
        }

        $changedAfter = $req->getQueryParam('changedAfter');
        if ($changedAfter != null) {
            $query->andWhere(['>=', 'changedAt', $changedAfter]);
        }

        if ($query->where == null) {
            return [];
        }

        switch ($this->modelClass) {
            case Book::class :
            case Category::class :
                $result = $query->asArray()->all();
                break;
            default :
                $result = [];
        }

        return $result;
    }

    /**
     * @return array|void
     * @throws BadRequestHttpException
     */
    public function actionCreate()
    {
        throw new BadRequestHttpException();
    }

    /**
     * Обновление атрибутов
     *
     * @return array
     * @throws NotAcceptableHttpException
     * @throws Exception
     */
    public function actionUpdateAttribute()
    {
        /** @var ActiveRecord $model */
        $model = null;
        /** @var ActiveRecord $class */
        $class = $this->modelClass;
        $request = Yii::$app->request;
        $success = false;
        $saved = array();
        if ($request->isPost) {
            $params = $request->bodyParams;
            if ($params['attribute'] != null) {
                $model = $class::find()->where(['uuid' => $params['modelUuid']])->one();
                if ($model != null) {
                    $model[$params['attribute']] = $params['value'];
                }
            } else {
                $model = new $class;
                $dataArray = json_decode($params['value'], true);
                $model->load($dataArray, '');
            }

            if ($model->save()) {
                $saved = $params['_id'];
                $success = true;
            }

            return ['success' => $success, 'data' => $saved];
        } else {
            throw new NotAcceptableHttpException();
        }
    }

    protected function createBase()
    {
        $request = Yii::$app->request;

        $rawData = $request->getRawBody();
        if ($rawData == null) {
            return [];
        }

        // список записей
        $items = json_decode($rawData, true);
        if (!is_array($items)) {
            return [];
        }

        foreach ($items as $key => $item) {
            unset($items[$key]['_id']);
        }

        // сохраняем записи
        $saved = self::createSimpleObjects($items);
        return $saved;
    }

    /**
     * @param array $items
     * @return array
     */
    protected function createSimpleObjects($items)
    {
        $success = true;
        $saved = array();
        foreach ($items as $item) {
            $line = self::createSimpleObject($item);
            if ($line->save()) {
                $saved[] = [
                    '_id' => $line->getAttribute('_id'),
                    'uuid' => isset($item['uuid']) ? $item['uuid'] : '',
                ];
            } else {
                $success = false;
            }
        }

        return ['success' => $success, 'data' => $saved];
    }

    /**
     * @param array $item
     * @return ActiveRecord
     */
    protected function createSimpleObject($item)
    {
        /** @var ActiveRecord $class */
        /** @var ActiveRecord $line */
        $class = $this->modelClass;
        $line = $class::findOne(['uuid' => $item['uuid']]);
        if ($line == null) {
            $line = new $class;
        }

        $line->setAttributes($item, false);
        return $line;
    }

}
