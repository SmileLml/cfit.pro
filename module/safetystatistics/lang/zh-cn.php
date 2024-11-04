<?php

$lang->safetystatistics->common      = '统计-系统';
$lang->safetystatistics->browse      = '安全数据表';
$lang->safetystatistics->params      = '基础参数指标';
$lang->safetystatistics->createscore = '重新生成';
$lang->safetystatistics->title       = '提示';
$lang->safetystatistics->errorMsg    = '重新生成失败，请重试';

$lang->safetystatisstics->title          = '安全数据统计表';
$lang->safetystatisstics->menuTitle[0]   = '统计分类';
$lang->safetystatisstics->menuTitle[1]   = '基础数据配置';
$lang->safetystatisstics->menuName[0][0] = '安全数据表|safetystatistics|browse';
$lang->safetystatisstics->menuName[1][1] = '基础参数指标|safetystatistics|params';
$lang->safetystatisstics->tableTitle[0]  = '指标权重';
$lang->safetystatisstics->tableTitle[1]  = '指标权重';
$lang->safetystatisstics->tableTitle[2]  = '标定值';
$lang->safetystatisstics->create         = '重新生成';

$lang->safetystatisstics->num         = '序号';
$lang->safetystatisstics->appId       = '系统名称';
$lang->safetystatisstics->targetTwo   = '检测项';
$lang->safetystatisstics->targetThree = '问题级别';
$lang->safetystatisstics->count       = '问题数量';
$lang->safetystatisstics->riskValue   = '单项风险值';
$lang->safetystatisstics->scoreValue  = '单项评分';
$lang->safetystatisstics->risk        = '系统风险总值';
$lang->safetystatisstics->score       = '综合评分';

//安全性基础参数指标
$lang->safetystatistics->targetOneList['static']     = '静态安全性评价';
$lang->safetystatistics->targetOneList['dynamic']    = '动态安全性评价';
$lang->safetystatistics->targetOneList['standard']   = '标准合规类评价';
$lang->safetystatistics->targetTwoList['source']     = '源码';
$lang->safetystatistics->targetTwoList['module']     = '组件';
$lang->safetystatistics->targetTwoList['master']     = '主机';
$lang->safetystatistics->targetTwoList['permeate']   = '渗透';
$lang->safetystatistics->targetTwoList['cip']        = '等保';
$lang->safetystatistics->targetThreeList['severity'] = '严重(P1)';
$lang->safetystatistics->targetThreeList['ordinary'] = '一般(P2)';
$lang->safetystatistics->targetThreeList['slight']   = '轻微(P3)';
$lang->safetystatistics->targetThreeList['suggest']  = '建议(P4)';

//安全性基础参数标定
$lang->safetystatistics->calibration['source']    = '源码标定值';
$lang->safetystatistics->calibration['module']    = '组件标定值';
$lang->safetystatistics->calibration['master']    = '主机标定值';
$lang->safetystatistics->calibration['permeate']  = '渗透标定值';
$lang->safetystatistics->calibration['cip']       = '等保标定值';
$lang->safetystatistics->calibration['composite'] = '综合标定值';

//一级指标和二级指标的关系
$lang->safetystatistics->target['source']   = 'static';
$lang->safetystatistics->target['module']   = 'static';
$lang->safetystatistics->target['master']   = 'static';
$lang->safetystatistics->target['permeate'] = 'dynamic';
$lang->safetystatistics->target['cip']      = 'standard';

$lang->safetystatistics->error->weightNum   = '%s下的%s的权重值必须大于0！';
$lang->safetystatistics->error->calibration = '%s必须大于0！';
$lang->safetystatistics->error->weightSum   = '参数指标总和必须等于1！';
