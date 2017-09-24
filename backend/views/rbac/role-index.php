<a href="<?=\yii\helpers\Url::to(['rbac/add-role'])?>"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>

<table class="table table-bordered table-hover">
    <tr>
        <th>角色名称</th>
        <th>角色描述</th>
        <th>操作</th>
    </tr>
    <?php foreach ($roles as $role):?>
        <tr>
            <td><?=$role->name?></td>
            <td><?=$role->description?></td>
            <td><a href="<?=\yii\helpers\Url::to(['rbac/del-role','name'=>$role->name])?>"><span class="glyphicon glyphicon-trash"></span></a>
                <a href="<?=\yii\helpers\Url::to(['rbac/edit-role','name'=>$role->name])?>"<span class="glyphicon glyphicon-cog" aria-hidden="true"></span></a>
            </td>
        </tr>
    <?php endforeach;?>
</table>