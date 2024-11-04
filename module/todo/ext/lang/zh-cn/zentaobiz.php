<?php
global $app;
if(!empty($app->user->feedback) or !empty($_COOKIE['feedbackView']))
{
    unset($lang->todo->typeList['bug']);
    unset($lang->todo->typeList['task']);
}
$lang->side->feedback = '反馈';
$lang->todo->feedback = '反馈';
$lang->todo->typeList['feedback'] = '反馈';
