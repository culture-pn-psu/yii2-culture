<?php

namespace backend\modules\person\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "position".
 *
 * @property integer $id
 * @property string $title
 * @property integer $parent_id
 * @property integer $type
 * @property integer $sort
 *
 * @property PersonPosition[] $personPositions
 * @property Position $parent
 * @property Position[] $positions
 */
class Position_2 extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'position';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['parent_id', 'type', 'sort'], 'integer'],
            [['title'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('person', 'รหัสตำแหน่ง'),
            'title' => Yii::t('person', 'ตำแหน่ง'),
            'parent_id' => Yii::t('person', 'ในฝ่ายงาน'),
            'type' => Yii::t('person', 'ฝ่าย'),
            'sort' => Yii::t('person', 'เรียง'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPersonPositions() {
        return $this->hasMany(PersonPosition::className(), ['position_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent() {
        return $this->hasOne(Position::className(), ['id' => 'parent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPositions() {
        return $this->hasMany(Position::className(), ['parent_id' => 'id']);
    }

    public function getUnders() {
        return $this->hasOne(Position::className(), ['id' => 'under']);
    }

    public static function getList() {
        $model = self::find()
                ->where(['type' => '1'])
                ->orderBy('id')
                ->all();
        foreach ($model as $val) {
            $arr[$val->title] = ArrayHelper::map($val->positions
                            , 'id', 'title'
            );
        }
//        echo "<pre>";
//        print_r($arr);
//        exit();
        return $arr;
    }

    public $numUl = [];

    public static function getTree($id) {
        $model = Position::find()->where(['id' => $id])->one();
        $str = '<figure class="org-chart cf">
    <ul class="administration">
      <li>					
        <ul class="director">
          <li>
                <a href="#" id="top_head"><span>' . $model->title . '</span></a>';
        $str .= $model->getTreeUnder($model->id);
        $str .= '</li></ul></li></ul></div><div class="clear"></div>
</div></figure>';
        return $str;
    }

    public function getTreeUnder($id, $person = null) {
        $positions = Position::find()->where(['under' => $id,])->orderBy('sort')->all();

        if ($positions) {
            $str = '';
            $str .= $person ? $person['start'] : '';
            $str .= '<ul class="departments cf">';
            foreach ($positions as $model) {
                $str .= '<li class="department">';
                $str .= '<a href="#" id="head"><span>' . $model->title . '</span></a>';
                $str .= $this->getTreeUnder($model->id, $this->getTreePerson($model->id));
                $str .= '</li>';
            }
            $str .= '</ul>';
            $str .= $person ? $person['end'] : '';
            return $str;
        } elseif ($person) {
            $str = $person ? $person['start'] . $person['end'] : '';
            return $str;
        }
        return false;
    }

    public function getTreePerson($id) {

        $model = PersonPosition::find()->where(['position_id' => $id])->orderBy(['sort' => 'desc'])->all();
        if ($model) {
            $str = '';
            $str_end = '';
            foreach ($model as $val) {
                $str.="<ul class='sections'><li class='section'>";
                
                $str.="<a class='person' data='" . $val->person->id . "'>";
                $str.='<div id="per-item"><div id="per-pic">';
                $str.='<img src="' . $val->person->imgTemp . '" width="50"/>';
                $str.='</div><div id="per-detail">';
                $str.= "<p><b>" . $val->person->fullname . "</b></p>";
                $str.= "<p>";
                $str.=$val->position->title;
                $str.= "</p>";
                $str.= "<hr/>";
                $str.= "<p></p>";
                $str.= "<p>ติดต่อ: " . $val->person->tel . "</p>";
                //$str.= "emial : ".$val->person->email;
                $str.= "</div></div></a>";
                $str_end.="</li></ul>";
            }
            //$str .= '</ul>';
            return ['start' => $str, 'end' => $str_end];
        }
        return false;
    }

    public function getUnderList() {
        return $this->hasMany(Position::className(), ['id' => 'under']);
    }

}
