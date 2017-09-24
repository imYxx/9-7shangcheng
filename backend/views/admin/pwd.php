<?php
$form = \yii\bootstrap\ActiveForm::begin();//<form>
echo $form->field($message,'password_hash')->textInput();
echo $form->field($message,'newpassword')->passwordInput();
echo $form->field($message,'reppassword')->passwordInput();
echo \yii\bootstrap\Html::submitButton('修改',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();//</form>