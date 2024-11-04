<?php
 $config->excel->editor['application'] = array('desc');
 $config->excel->editor['problem']     = array('desc', 'reason', 'solution', 'progress', 'plateMakAp', 'plateMakInfo'); //220408 新增 对问题池 excel 列宽度
 $config->excel->editor['demand']      = array('conclusion', 'reason', 'solution', 'progress', 'plateMakAp', 'plateMakInfo'); //220408 新增 对需求池 excel 列宽度
 $config->excel->editor['modify']      = array('desc','target','effect','plan','risk','operation','reason','test','step', 'conclusion', 'solution','progress');
 $config->excel->editor['fix']        = array('operation','test','fixResult','fixReason', 'fixDesc','fixStep','checkList'); //220224 新增 对数据修正 excel 列宽度
 $config->excel->editor['gain']       = array('gainDesc','gainReason','gainPurpose','test','gainStep','checkList'); //220224 新增 对数据获取 excel 列宽度
 $config->excel->editor['demandcollection']   = array('title','desc','analysis');
 $config->excel->editor['reviewissue']     = array('review','desc','title','dealDesc');
 $config->excel->editor['reviewproblem']   = array('review','desc','title','dealDesc');

