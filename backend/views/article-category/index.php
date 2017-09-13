<table class="table table-bordered table-hover">
    <tr class="danger">
        <th >id</th>
        <th>文章名称</th>
        <th>介绍</th>
        <th>排序</th>
        <th>是否上架</th>
        <th>操作</th>
    </tr>
    <?php foreach($models as $model):?>
        <tr>
            <td ><?=$model->id?></td>
            <td ><?=$model->name?></td>
            <td><?=$model->intro?></td>
            <td><?=$model->sort?></td>
            <td><?=$model->status?'是':'否'?></td>
            <td>
                <a href="<?=\yii\helpers\Url::to(['article-category/add'])?>"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>
                <a href="<?=\yii\helpers\Url::to(['article-category/del','id'=>$model->id])?>"<span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>
                <a href="<?=\yii\helpers\Url::to(['article-category/edit','id'=>$model->id])?>"<span class="glyphicon glyphicon-cog" aria-hidden="true"></span></a>
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

