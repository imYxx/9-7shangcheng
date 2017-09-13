<a href="<?=\yii\helpers\Url::to(['goods-category/add'])?>"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>
<table class="table table-bordered table-hover">
    <tr class="info">
        <th >id</th>
        <th>分类名</th>
        <th>介绍</th>
        <th>操作</th>
    </tr>
    <?php foreach($goods_categorys as $goods_category):?>
        <tr data-id="<?= $goods_category->id?>">
            <td ><?= $goods_category->id?></td>
            <td ><?= $goods_category->name?></td>
            <td ><?= $goods_category->intro?></td>
            <td>
                <a href="<?=\yii\helpers\Url::to(['goods-category/del','id'=>$goods_category->id])?>" class="btn  del_btn"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></span></a>
                <a href="<?=\yii\helpers\Url::to(['goods-category/edit','id'=>$goods_category->id])?>"<span class="glyphicon glyphicon-cog" aria-hidden="true"></span></a>
            </td>
        </tr>
    <?php endforeach;?>
</table>


