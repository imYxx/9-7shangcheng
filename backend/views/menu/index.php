<p><h3>菜单列表</h3></p>
<a href="<?=\yii\helpers\Url::to(['menu/add'])?>" class="btn btn-info">添加</a>
    <table class="table table-bordered table-hover">
        <tr>
            <th >名称</th>
            <th>路由</th>
            <th>排序</th>
            <th>操作</th>
        </tr>
        <?php foreach($models as $model):?>
            <tr data-id="<?=$model->id?>">
                <td >

                    <?=str_repeat('------------',($model->parent_id?1:0)).$model->name?>
                </td>
                <td ><?=$model->url?></td>
                <td ><?=$model->sort?></td>
                <td>
                    <a href="javascript:;" class="btn  del_btn">删除</a>
                    <a href="<?=\yii\helpers\Url::to(['menu/edit','id'=>$model->id])?>">修改</a>
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
$del_url = \yii\helpers\Url::to(['menu/del']);
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
