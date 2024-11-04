<?php
global $app;
if(!empty($app->user->feedback) or !empty($_COOKIE['feedbackView']))
{
 unset($lang->todo->typeList['bug']);
 unset($lang->todo->typeList['task']);
}
$lang->side->feedback = 'Phản hồi';
$lang->todo->feedback = 'Phản hồi';
$lang->todo->typeList['feedback'] = 'Phản hồi';
