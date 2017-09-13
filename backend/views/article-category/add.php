<?php
$form = \yii\bootstrap\ActiveForm::begin();//<form>
echo $form->field($model,'name')->textInput();
echo $form->field($model,'intro')->textInput();
echo $form->field($model,'sort')->textInput();
echo $form->field($model,'status')->checkbox();
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();//</form>