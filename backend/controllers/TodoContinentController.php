<?php

namespace frontend\controllers;

use common\models\TodoContinent;
use frontend\models\TodoContinentSearch;
use Throwable;
use Yii;
use yii\db\StaleObjectException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * TodoContinentController implements the CRUD actions for TodoContinent model.
 */
class TodoContinentController extends ParentController
{
    protected $modelClass = TodoContinent::class;

    /**
     * Lists all TodoContinent models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TodoContinentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 15;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Continent model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Finds the TodoContinent model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TodoContinent the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TodoContinent::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Creates a new TodoContinent model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TodoContinent();
        $searchModel = new TodoContinentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 50;

        if ($model->load(Yii::$app->request->post())) {
            echo $model->latitude;
            echo $model->longitude;
            $file = UploadedFile::getInstance($model, 'image');
            if ($file && $file->tempName) {
                $fileName = self::_saveFile($model, $file);
                if ($fileName) {
                    $model->image = $fileName;
                }
            }
            if ($model->save(false)) {
                $searchModel = new TodoContinentSearch();
                $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
                $dataProvider->pagination->pageSize = 50;
                return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                ]);
            }
        } else {
            return $this->render('create', [
                'model' => $model, 'dataProvider' => $dataProvider
            ]);
        }
    }

    /**
     * @param TodoContinent $model
     * @param UploadedFile $file
     *
     * @return string | null
     */
    private static function _saveFile($model, $file)
    {
        $dir = $model->getImageDir();
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0755, true)) {
                return null;
            }
        }

        $targetDir = Yii::getAlias($dir);
        $fileName = $model->uuid . '.' . $file->extension;
        if ($file->saveAs($targetDir . $fileName)) {
            return $fileName;
        } else {
            return null;
        }
    }

    /**
     * Updates an existing TodoContinent model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $oldImage = $model->image;
        if ($model->load(Yii::$app->request->post())) {
            // получаем изображение для последующего сохранения
            $file = UploadedFile::getInstance($model, 'image');
            if ($file && $file->tempName) {
                $fileName = self::_saveFile($model, $file);
                if ($fileName) {
                    $model->image = $fileName;
                } else {
                    $model->image = $oldImage;
                }
            } else {
                $model->image = $oldImage;
            }
            echo $model->image;
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->_id]);
            }
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing TodoContinent model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
}
