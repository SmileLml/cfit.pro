<?php
$lang->action->objectTypes['feedback'] = '反馈';

$lang->action->label->feedback = '反馈|feedback|view|id=%s';

$lang->action->desc->asked        = '$date, 由 <strong>$actor</strong> 追问。' . "\n";
$lang->action->desc->replied      = '$date, 由 <strong>$actor</strong> 回复。' . "\n";
$lang->action->desc->tobug        = '$date, 由 <strong>$actor</strong> 转为Bug <strong>$extra</strong>。' . "\n";
$lang->action->desc->tostory      = '$date, 由 <strong>$actor</strong> 转为' . $lang->SRCommon . ' <strong>$extra</strong>。' . "\n";
$lang->action->desc->totask       = '$date, 由 <strong>$actor</strong> 转为任务 <strong>$extra</strong>。' . "\n";
$lang->action->desc->fromfeedback = '$date, 由 <strong>$actor</strong> 从<strong>反馈</strong>转化而来，反馈编号为 <strong>$extra</strong>。' . "\n";
$lang->action->desc->totodo       = '$date, 由 <strong>$actor</strong> 转待办 <strong>$extra</strong>。' . "\n";

$lang->action->label->asked        = '追问了';
$lang->action->label->replied      = '回复了';
$lang->action->label->tobug        = '转bug';
$lang->action->label->tostory      = '转' . $lang->SRCommon;
$lang->action->label->totask       = '转任务';
$lang->action->label->totodo       = '转待办';
$lang->action->label->fromfeedback = '由反馈创建';
