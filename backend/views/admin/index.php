
<a href="<?=\yii\helpers\Url::to(['admin/add'])?>" class="btn btn-warning">添加用户</a>

<a href="<?=\yii\helpers\Url::to(['admin/pwd'])?>"  class="btn btn-danger">修改密码</a>


<table class="table table-bordered table-hover">
    <tr class="warning">
        <th >id</th>
        <th>用户名</th>
        <th>头像</th>
        <th>邮箱</th>
        <th>状态</th>
        <th>创建时间</th>
        <th>最后登录时间</th>
        <th>最后登录IP</th>
        <th>操作</th>
    </tr>
    <?php foreach($models as $model):?>
        <tr>
            <td ><?=$model->id?></td>
            <td ><?=$model->username?></td>
            <td><img src="<?=$model->logo?>" width="90px"   class="img-rounded"></td>
            <td ><?=$model->email?></td>
            <td><?=$model->status?'正常':'回收站'?></td>
            <td><?=date('Y-m-d h:i:s',$model->created_at)?></td>
            <td><?=date('Y/m/d h:i:s',$model->last_login_time)?></td>
            <td><?=$model->last_login_ip?$model->last_login_ip:'未登录过'?></td>
            <td>
                <a href="<?=\yii\helpers\Url::to(['admin/del','id'=>$model->id])?>"><span class="glyphicon glyphicon-trash"></span></a>
                <a href="<?=\yii\helpers\Url::to(['admin/edit','id'=>$model->id])?>"<span class="glyphicon glyphicon-cog" aria-hidden="true"></span></a>

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
