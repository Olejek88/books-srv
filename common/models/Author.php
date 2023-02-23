<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\Expression;

/**
 * This is the model class for table "book".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $title
 * @property string $description
 * @property string $imageUrl
 * @property string $createdAt
 * @property string $changedAt
 */
class Author extends RootModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'author';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'createdAt',
                'updatedAtAttribute' => 'changedAt',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid', 'title', 'description'], 'required'],
            [['createdAt', 'changedAt'], 'safe'],
            [['uuid', 'imageUrl'], 'string', 'max' => 150],
            [['title', 'description'], 'string', 'max' => 500],
            [['uuid', 'title'], 'filter', 'filter' => function ($param) {
                return htmlspecialchars($param, ENT_QUOTES | ENT_HTML401);
            }
            ],
        ];
    }

    /**
     * @return array
     */
    public function fields()
    {
        return ['uuid', 'title', 'imageUrl', 'description', 'imageUrl'];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('app', '№'),
            'uuid' => Yii::t('app', 'Uuid'),
            'title' => Yii::t('app', 'Title'),
            'description' => Yii::t('app', 'Description'),
            'imageUrl' => Yii::t('app', 'Image'),
            'createdAt' => Yii::t('app', 'Created'),
            'changedAt' => Yii::t('app', 'Changed'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(
            Author::class, ['uuid' => 'authorUuid']
        );
    }
}
