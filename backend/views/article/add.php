<?php

$form = \yii\bootstrap\ActiveForm::begin();//<form>
echo $form->field($model,'name')->textInput();
echo $form->field($model,'intro')->textInput();
echo $form->field($model,'brand_id')->dropDownList($data);
echo $form->field($content,'content')->textarea(['rows'=>10]);
echo $form->field($model,'sort')->textInput();
echo $form->field($model,'status',['inline'=>true])->radioList([1=>'是',-1=>'否']);

echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();//</form>