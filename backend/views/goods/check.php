<div class="container box">
    <?php foreach ($model as $v):?>
    <div>
        <img src="<?=$v['path']?>">
        <input type="button" value="删除" class="btn btn-danger">
    </div>
<?php endforeach;?>
</div>
<div>
    <img src="" alt="" class='image'>
</div>


<?php
//--------uploadfive------------------

$url=\yii\helpers\Url::to(['goods/check']);
$goods_id=$_GET['id'];
use yii\web\JsExpression;

//外部TAG
echo \yii\bootstrap\Html::fileInput('test', NULL, ['id' => 'test']);
echo \flyok666\uploadifive\Uploadifive::widget([
    'url' => yii\helpers\Url::to(['s-upload']),
    'id' => 'test',
    'csrf' => true,
    'renderTag' => false,
    'jsOptions' => [
        'formData'=>['someKey' => 'someValue'],
        'width' => 120,
        'height' => 40,
        'onError' => new JsExpression(<<<EOF
            function(file, errorCode, errorMsg, errorString) {
            console.log('The file ' + file.name + ' could not be uploaded: ' + errorString + errorCode + errorMsg);
    }
EOF
        ),
        'onUploadComplete' => new JsExpression(<<<EOF
            function(file, data, response) {
            data = JSON.parse(data);
            if (data.error) {
            console.log(data.msg);
            } else {

            $.getJSON("$url",{'goods_id':"$goods_id",'fileUrl':data.fileUrl},function(msg){
                if(msg){
                   var html='<img src='+data.fileUrl+'>'+'<input type="button" value="删除" class="btn btn-danger">';
                   $('.box').append(html);
                }
            })

            //添加div

    }
    }
EOF
        ),
    ]
]);

//------------end------------------------

$url=\yii\helpers\Url::to(['goods/ajax']);
/**
 * @var $this \yii\web\View
 */
$this->registerJs(new JsExpression(
    <<<JS
                $(document).on('click','.btn',function(){
                //console.log($(this));
                //移除img
                $(this).closest('div').fadeOut('slow');
                //发送ajax请求删除数据库对应图片
                //需要获取该图片的url地址
                var path=$(this).prev().attr('src');
                //console.log(path);
               $.getJSON("$url",{'fileUrl':path},function(info) {
                    console.log(info);
                })
            })
JS

));

