<?php
global $app;
if(!empty($app->user->feedback) or !empty($_COOKIE['feedbackView']))
{
    unset($lang->todo->typeList['bug']);
    unset($lang->todo->typeList['task']);
}
$lang->side->feedback = 'Feedback';
$lang->todo->feedback = 'Feedback';
$lang->todo->typeList['feedback'] = 'Feedback';
