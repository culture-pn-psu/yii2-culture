<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\BaseStringHelper;
use backend\modules\article\models\Article;
use backend\modules\article\models\ArticleCategory;

$arts = Article::find()->where(['article_category_id'=>$article_category_id])->all();
$artCate = ArticleCategory::findOne($article_category_id);
$link=(isset($link))?$link:'article';
?>
<?php if(!empty($arts)){ ?>
<div class="box">
    <?= Html::tag('h3',$artCate->title,['class'=>'text-left wow fadeInDown article-head']) ?>
    <small></small>
    <div class="row">
        <div class="col-sm-12">
            <?php 
                    foreach($arts as $k=>$model){
                        $url = Url::to(['/'.$link,'id'=>$model->id]);    
                        
                        
                        if($k==0){
                        echo '<div class="col-sm-6">';
                        echo $this->render(
                        '/article/_itemHome',
                        [
                            'model' => $model,
                            'asset'=>$asset,
                            'url' => $url,
                            ]
                        );
                        echo '</div><div class="col-sm-6">';
                        }else{
                        echo $this->render(
                        '/article/_listItem',
                        [
                            'model' => $model,
                            'asset'=>$asset,
                            'url' => $url,
                            ]
                        );
                        } 
                    } 
                    echo "</div>";
                    ?>
            
           <?=Html::a('อ่านทั้งหมด',['/'.$link],['style'=>'color:#fff !important;font-size:19px;','class'=>'pull-right'])?>            
        </div>
    </div>
</div>




           

<?php } ?>