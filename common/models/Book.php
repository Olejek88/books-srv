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
 * @property string $link
 * @property string $authorUuid
 * @property Author $author
 * @property string $authorName
 * @property string $description
 * @property string $categoryUuid
 * @property Category $category
 * @property string $imageUrl
 * @property string $createdAt
 * @property string $changedAt
 */
class Book extends RootModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'book';
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
            [['uuid', 'title', 'author', 'authorUuid', 'description', 'categoryUuid', 'link'], 'required'],
            [['createdAt', 'changedAt'], 'safe'],
            [['uuid', 'categoryUuid', 'authorName', 'authorUuid', 'imageUrl'], 'string', 'max' => 150],
            [['title', 'author', 'imageUrl', 'amazonImage', 'link'], 'string', 'max' => 500],
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
        return ['uuid',
            'category' => function ($model) {
                return $model->category;
            },
            'title', 'author', 'authorName', 'authorUuid',
            'imageUrl', 'description', 'categoryUuid', 'link', 'imageUrl', 'amazonImage'
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('app', '???'),
            'uuid' => Yii::t('app', 'Uuid'),
            'title' => Yii::t('app', 'Title'),
            'author' => Yii::t('app', 'Author'),
            'description' => Yii::t('app', 'Description'),
            'category' => Yii::t('app', 'Category'),
            'imageUrl' => Yii::t('app', 'Image'),
            'createdAt' => Yii::t('app', 'Created'),
            'changedAt' => Yii::t('app', 'Changed'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(
            Category::class, ['uuid' => 'categoryUuid']
        );
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
