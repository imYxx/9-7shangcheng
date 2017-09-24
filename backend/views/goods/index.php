<a href="<?=\yii\helpers\Url::to(['goods/add'])?>"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>
<div>
    <form action="<?=\yii\helpers\Url::to(['goods/index'])?>" method="get">
    <input type="text" name="name" placeholder="商品名称"/>
    <input type="text" name="sn" placeholder="货号"/>
    <input type="text" name="cprice" placeholder="价格从"/>
    <input type="text" name="dprice" placeholder="到"/>

        <input type="submit" value=" 搜索 "class="btn btn-default" />
    </form>


</div>

<table class="table table-bordered table-hover">
    <tr class="info">
        <th >id</th>
        <th>商品名称</th>
        <th>商品图片</th>
        <th>商品分类</th>
        <th>品牌分类</th>
        <th>市场价格</th>
        <th>商品价格</th>
        <th>库存</th>
        <th>是否在售</th>
        <th>状态</th>
        <th>排序</th>
        <th>添加时间</th>
        <th>货号</th>
        <th>阅览次数</th>
        <th>操作</th>
    </tr>
    <?php foreach($models as $model):?>
        <tr>
            <td ><?=$model->id?></td>
            <td ><?=$model->name?></td>
            <td><img src="<?=$model->logo?>" width="90px"></td>
            <td><?=$model->goodsCategory->name?></td>
            <td><?=$model->brand->name?></td>
            <td><?=$model->market_price?></td>
            <td><?=$model->shop_price?></td>
            <td><?=$model->stock?></td>
            <td><?=$model->is_on_sale?'在售':'已下架'?></td>
            <td><?=$model->status?'正常':'回收站'?></td>
            <td><?=$model->sort?></td>
            <td><?=date('Y-m-d H:i:s',$model->create_time)?></td>
            <td><?=$model->sn?></td>
            <td><?=$model->view_times?></td>
            <td>
                <a href="<?=\yii\helpers\Url::to(['goods/del','id'=>$model->id])?>"><span class="glyphicon glyphicon-trash"></span></a>
                <a href="<?=\yii\helpers\Url::to(['goods/edit','id'=>$model->id])?>"<span class="glyphicon glyphicon-cog" aria-hidden="true"></span></a>
                <a href="<?=\yii\helpers\Url::to(['goods/check','id'=>$model->id])?>"<span class="glyphicon glyphicon-picture" aria-hidden="true"></span></a>

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
