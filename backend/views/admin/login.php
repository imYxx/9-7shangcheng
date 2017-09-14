<?php
$form = \yii\bootstrap\ActiveForm::begin();//<form>
echo $form->field($model,'username')->textInput();
echo $form->field($model,'password_hash')->passwordInput();
echo $form->field($model,'code')->widget(\yii\captcha\Captcha::className(),['captchaAction'=>'admin/captcha']);
echo $form->field($model,'remember')->checkbox([1=>'记住我',0=>'忘掉我']);
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();//</form>