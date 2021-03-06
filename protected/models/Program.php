<?php

/**
 * This is the model class for table "program".
 *
 * The followings are the available columns in table 'program':
 * @property integer $id
 * @property integer $channelId
 * @property integer $titleId
 * @property string $startTime
 * @property string $endTime
 * @property string $lastUpdate
 * @property string $subTitle
 * @property integer $flag
 * @property integer $deleted
 * @property integer $warn
 * @property integer $revision
 * @property integer $allDay
 *
 * The followings are the available model relations:
 * @property Channel $channel
 * @property Title $title
 * @property Check $check
 */
class Program extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Program the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'program';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array(
                'channelId, titleId, startTime, endTime, lastUpdate, flag, deleted, warn, revision, allDay',
                'required'
            ),
            array('channelId, titleId, flag, deleted, warn, revision, allDay', 'numerical', 'integerOnly' => true),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array(
                'id, channelId, titleId, startTime, endTime, lastUpdate, subTitle, flag, deleted, warn, revision, allDay, title, channel, category',
                'safe',
                'on' => 'search'
            ),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'channel' => array(self::BELONGS_TO, 'Channel', 'channelId'),
            'title' => array(self::BELONGS_TO, 'Title', 'titleId'),
            'check' => array(self::HAS_ONE, 'Check', array('titleId' => 'titleId', 'channelId' => 'channelId')),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'channelId' => 'Channel',
            'titleId' => 'Title',
            'startTime' => 'Start Time',
            'endTime' => 'End Time',
            'lastUpdate' => 'Last Update',
            'subTitle' => 'Sub Title',
            'flag' => 'Flag',
            'deleted' => 'Deleted',
            'warn' => 'Warn',
            'revision' => 'Revision',
            'allDay' => 'All Day',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new CDbCriteria;
        $criteria->group = 't.titleId,t.channelId';
        $criteria->with = array(
            'title',
            'channel',
        );
//        $criteria->compare('title.category', 1);

        $criteria->compare('id', $this->id);
        $criteria->compare('channelId', $this->channelId);
        $criteria->compare('titleId', $this->titleId);
        $criteria->compare('startTime', $this->startTime, true);
        $criteria->compare('endTime', $this->endTime, true);
        $criteria->compare('lastUpdate', $this->lastUpdate, true);
        $criteria->compare('subTitle', $this->subTitle, true);
        $criteria->compare('flag', $this->flag);
        $criteria->compare('deleted', $this->deleted);
        $criteria->compare('warn', $this->warn);
        $criteria->compare('revision', $this->revision);
        $criteria->compare('allDay', $this->allDay);
        $criteria->compare('title', $this->title, true);
        $criteria->compare('channel.name', $this->channel, true);
        //		$criteria->compare('category', $this->title->category, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,

        ));
    }

    public function checkedSearch($botId)
    {
        $criteria = new CDbCriteria;
        $criteria->group = 't.titleId,t.channelId';
        $criteria->with = array(
            'title',
            'channel',
        );
        $criteria->join = 'INNER JOIN `check` AS c ON t.channelId = c.channelId AND c.titleId = t.titleId AND c.botId = ' . $botId;
        $criteria->order = 'startTime ASC';

        $criteria->compare('id', $this->id);
        $criteria->compare('channelId', $this->channelId);
        $criteria->compare('titleId', $this->titleId);
        $criteria->compare('startTime', $this->startTime, true);
        $criteria->compare('endTime', $this->endTime, true);
        $criteria->compare('lastUpdate', $this->lastUpdate, true);
        $criteria->compare('subTitle', $this->subTitle, true);
        $criteria->compare('flag', $this->flag);
        $criteria->compare('deleted', $this->deleted);
        $criteria->compare('warn', $this->warn);
        $criteria->compare('revision', $this->revision);
        $criteria->compare('allDay', $this->allDay);
        $criteria->compare('title', $this->title, true);
        $criteria->compare('channel.name', $this->channel, true);
        //		$criteria->compare('category', $this->title->category, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function now($botId)
    {
        $criteria = new CDbCriteria;
        $criteria->with = array(
            'title',
            'channel',
        );
        $criteria->join = 'INNER JOIN `check` AS c ON t.channelId = c.channelId AND c.titleId = t.titleId AND c.botId=' . (int)$botId;
        $criteria->order = 'startTime ASC';
        $criteria->compare('startTime', '<=', time());
        return self::model()->findAll($criteria);
        //		return new CActiveDataProvider($this, array(
        //			'criteria' => $criteria,
        //		));
    }

    public function getNowArray($botId)
    {
        $models = $this->now($botId);
        return $this->convertModelToArray($models);

    }

    public function convertModelToArray($models)
    {
        if (is_array($models)) {
            $arrayMode = true;
        } else {
            $models = array($models);
            $arrayMode = false;
        }

        $result = array();
        foreach ($models as $model) {
            $attributes = $model->getAttributes();
            $relations = array();
            foreach ($model->relations() as $key => $related) {
                if ($model->hasRelated($key)) {
                    $relations[$key] = $this->convertModelToArray($model->$key);
                }
            }
            $all = array_merge($attributes, $relations);

            if ($arrayMode) {
                array_push($result, $all);
            } else {
                $result = $all;
            }
        }
        return $result;
    }
}