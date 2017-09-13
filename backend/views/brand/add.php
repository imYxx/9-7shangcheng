<?php
use yii\web\JsExpression;
$form = \yii\bootstrap\ActiveForm::begin();//<form>
echo $form->field($model,'name')->textInput();
echo $form->field($model,'intro')->textInput();
echo $form->field($model,'sort')->textInput();
echo $form->field($model,'logo')->hiddenInput();
//==================updataifive插件
//Remove Events Auto Convert


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
        console.log(data.fileUrl);
        //将上传文件的路径写入在隐藏域的路径
        $("#brand-logo").val(data.fileUrl);
        //图片的回显
        $("#img").attr("src",data.fileUrl);

    }
}
EOF
        ),
    ]
]);




//===============================

echo \yii\bootstrap\Html::img($model->logo,['id'=>'img','width'=>"380"]);
echo $form->field($model,'status')->checkbox();
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();//</form>