<?php
?>
<a href="<?=\yii\helpers\Url::to(['article/index'])?>"><span class="glyphicon glyphicon-hand-left" aria-hidden="true"></span></a>
    <p></p>
    <p></p>
<?php foreach($contents as $content):?>
<p>
    <?=$content->content?>
</p>
<?php endforeach;?>