<?php

namespace common\models;

use common\components\behaviors\CacheInvalidateBehavior;
use trntv\filekit\behaviors\UploadBehavior;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\FileHelper;

/**
 * This is the model class for table "widget_carousel_item".
 *
 * @property integer $id
 * @property integer $carousel_id
 * @property string $base_url
 * @property string $path
 * @property string $type
 * @property string $imageUrl
 * @property string $url
 * @property string $caption
 * @property integer $status
 * @property integer $order
 *
 * @property WidgetCarousel $carousel
 */
class WidgetCarouselItem extends \yii\db\ActiveRecord
{

    /**
     * @var array|null
     */
    public $image;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%widget_carousel_item}}';
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $key = array_search('carousel_id', $scenarios[self::SCENARIO_DEFAULT], true);
        $scenarios[self::SCENARIO_DEFAULT][$key] = '!carousel_id';
        return $scenarios;
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            [
                'class' => UploadBehavior::className(),
                'attribute' => 'image'

            ],
            'cacheInvalidate'=>[
                'class'=>CacheInvalidateBehavior::className(),
                'keys'=>[
                    function ($model) {
                        return [
                            WidgetCarousel::className(),
                            $model->carousel->key
                        ];
                    }
                ]
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['carousel_id'], 'required'],
            [['carousel_id', 'status', 'order'], 'integer'],
            [['url', 'caption', 'base_url', 'path'], 'string', 'max' => 1024],
            [['type'], 'string', 'max' => 45],
            ['file', 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'carousel_id' => Yii::t('common', 'Carousel ID'),
            'image' => Yii::t('common', 'Image'),
            'base_url' => Yii::t('common', 'Base URL'),
            'path' => Yii::t('common', 'Path'),
            'type' => Yii::t('common', 'File Type'),
            'url' => Yii::t('common', 'Url'),
            'caption' => Yii::t('common', 'Caption'),
            'status' => Yii::t('common', 'Status'),
            'order' => Yii::t('common', 'Order')
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCarousel()
    {
        return $this->hasOne(WidgetCarousel::className(), ['id' => 'carousel_id']);
    }

    /**
     * @return string
     */
    public function getImageUrl()
    {
        return rtrim($this->base_url. '/') . '/' . $this->path;
    }
}
