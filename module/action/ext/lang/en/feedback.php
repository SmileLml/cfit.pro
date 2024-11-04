<?php
$lang->action->objectTypes['feedback'] = 'Feedback';

$lang->action->label->feedback = 'Feedback|feedback|view|id=%s';

$lang->action->desc->asked        = '$date, doubted by <strong>$actor</strong>.' . "\n";
$lang->action->desc->replied      = '$date, replied by <strong>$actor</strong>.' . "\n";
$lang->action->desc->tobug        = '$date, converted to Bug by <strong>$actor</strong>. Its ID was <strong>$extra</strong>.' . "\n";
$lang->action->desc->tostory      = '$date, converted to ' . $lang->SRCommon . ' by <strong>$actor</strong>. Its ID was <strong>$extra</strong>.' . "\n";
$lang->action->desc->totask       = '$date, converted to Task by <strong>$actor</strong>. Its ID was <strong>$extra</strong>.' . "\n";
$lang->action->desc->fromfeedback = '$date, created by <strong>$actor</strong> from <strong>feedback</strong>, the ID which was <strong>$extra</strong>.' . "\n";
$lang->action->desc->totodo       = '$date, converted to todo from <strong>$actor</strong>. Its ID was <strong>$extra</strong>.' . "\n";

$lang->action->label->asked        = 'doubted';
$lang->action->label->replied      = 'replied';
$lang->action->label->tobug        = 'convert to bug';
$lang->action->label->tostory      = 'convert to ' . $lang->SRCommon;
$lang->action->label->totask       = 'convert to task';
$lang->action->label->totodo       = 'convert to todo';
$lang->action->label->fromfeedback = 'from feedback';
