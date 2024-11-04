<?php
$filter->flow = new stdclass();
$filter->flow->ajaxgetmore = new stdclass();
$filter->flow->ajaxgetmore->get['search'] = 'reg::any';
$filter->flow->ajaxgetmore->get['limit']  = 'int';

$config->flowLimit = 0;
