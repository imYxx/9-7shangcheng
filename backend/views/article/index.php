<a href="<?=\yii\helpers\Url::to(['article/add'])?>"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>
<table class="table table-bordered table-hover">
    <tr class="danger">
        <th >id</th>
        <th>文章名称</th>
        <th>文章介绍</th>
        <th>文章分类</th>
        <th>排序</th>
        <th>是否上架</th>
        <th>创建时间</th>
        <th>操作</th>
    </tr>
    <?php foreach($models as $model):?>
        <tr data-id="<?=$model->id?>">
            <td ><?=$model->id?></td>
            <td ><?=$model->name?></td>
            <td><?=$model->intro?></td>
            <td><?=$model->article_category->name?></td>
            <td><?=$model->sort?></td>
            <td><?=$model->status?'是':'否'?></td>
            <td><?=date('Y/m/d H:i:s',$model->create_time)?></td>
            <td>
                <a href="javascript:;" class=" del_btn"><span class="glyphicon glyphicon-apple"></span></a>
                <a href="<?=\yii\helpers\Url::to(['article/edit','id'=>$model->id])?>"<span class="glyphicon glyphicon-cog" aria-hidden="true"></span></a>
                <a href="<?=\yii\helpers\Url::to(['article-detail/index','id'=>$model->id])?>"<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></a>

            </td>
        </tr>
    <?php endforeach;?>
</table>

<?php
//分页工具条
echo \yii\widgets\LinkPager::widget([
    'pagination'=>$pager,
    'nextPageLabel'=>'下一页',
    'prevPageLabel'=>'上一页'
]);
/**
 * @var $this \yii\web\View
 */
$del_url = \yii\helpers\Url::to(['article/del']);
//注册js代码
    $this->registerJs(new \yii\web\JsExpression(
                <<<JS
    $(".del_btn").click(function(){
         if(confirm('确定删除?')){
             var tr = $(this).closest('tr');
             var id = tr.attr("data-id");
            $.post("{$del_url}",{id:id},function(data){
                if(data == 'success'){
                    alert('删除成功');
                  tr.hide('slow');
               }else{
                   alert('删除失败');
               }
           });
       }
   });
JS
    ));

