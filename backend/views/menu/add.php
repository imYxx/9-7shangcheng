<?php
$form = \yii\bootstrap\ActiveForm::begin();//<form>
echo $form->field($model,'name')->textInput();
echo $form->field($model,'parent_id')->dropDownList($datas);
echo $form->field($model,'url')->dropDownList(\backend\models\RoleForm::getPermissionItems2());
echo $form->field($model,'sort')->textInput();
echo \yii\bootstrap\Html::submitButton('添加',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();//</form>是