

<a href="<?=\yii\helpers\Url::to(['rbac/add'])?>" class="btn btn-info">添加权限</a>
<!--第二步：添加如下 HTML 代码-->
<table id="table_id_example" class="display">
    <thead>
    <tr>
        <th>名称</th>
        <th>描述</th>
        <th>操作</th>
    </tr>
    </thead>

    <tbody>
        <?php foreach($permissions as $permission):?>
    <tr>
        <td><?=$permission->name?></td>
        <td><?=$permission->description?></td>
        <td><a href="<?=\yii\helpers\Url::to(['rbac/del','name'=>$permission->name])?>"><span class="glyphicon glyphicon-trash"></span></a>
            <a href="<?=\yii\helpers\Url::to(['rbac/edit','name'=>$permission->name])?>"<span class="glyphicon glyphicon-cog" aria-hidden="true"></span></a>
        </td>
    </tr>
        <?php endforeach;?>
    </tbody>

</table>

<?php
$this->registerJs(new \yii\web\JsExpression(
        <<<JS
        $(document).ready( function () {
        $('#table_id_example').DataTable();
        } );

JS

));
$this->registerCssFile('@web/media/css/jquery.dataTables.css');
$this->registerJsFile('@web/media/js/jquery.js');
$this->registerJsFile('@web/media/js/jquery.dataTables.js',['depends'=>\yii\web\JqueryAsset::className()]);

?>


