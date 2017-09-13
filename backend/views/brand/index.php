<table class="table table-bordered table-hover">
    <tr class="danger">
        <th >id</th>
        <th>品牌名</th>
        <th>介绍</th>
        <th>图标</th>
        <th>排序</th>
        <th>是否上架</th>
        <th>操作</th>
    </tr>
    <?php foreach($models as $model):?>
        <tr data-id="<?=$model->id?>">
            <td ><?=$model->id?></td>
            <td ><?=$model->name?></td>
            <td><?=$model->intro?></td>
            <td ><img src="<?=$model->logo?>" class="img-circle" width="80"/> </td>
            <td><?=$model->sort?></td>
            <td><?=$model->status?'是':'否'?></td>
            <td>
                <a href="<?=\yii\helpers\Url::to(['brand/add'])?>"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>
                <a href="javascript:;" class="btn  del_btn"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></span></a>
                <a href="<?=\yii\helpers\Url::to(['brand/edit','id'=>$model->id])?>"<span class="glyphicon glyphicon-cog" aria-hidden="true"></span></a>
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
$del_url = \yii\helpers\Url::to(['brand/del']);
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


