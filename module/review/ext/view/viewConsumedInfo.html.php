<div class="detail">
    <div class="detail-title"><?php echo $lang->consumedTitle;?></div>
    <div class='detail-content'>
        <table class='table table-data'>
            <tbody>
            <tr>
                <th class='w-80px'><?php echo $lang->review->nodeUser;?></th>
              <!--  <td class='text-center w-50px'><?php /*echo $lang->review->consumed;*/?></td>-->
                <td class='text-center  w-100px'><?php echo $lang->review->before;?></td>
                <td class='text-center w-130px'><?php echo $lang->review->after;?></td>
                <!-- <td class='text-left'><?php /*echo $lang->actions;*/?></td>-->
            </tr>
            <?php foreach($review->consumed as $index => $c):?>
                <tr>
                    <th class='w-80px'><?php echo zget($users, $c->account, $c->account);?></th>
                 <!--   <td class='text-center  w-50px'><?php /*echo $c->consumed . ' ' . $lang->hour;*/?></td>-->
                    <td class='text-center  w-100px'><?php echo zget($allstatus, $c->before, '-');?></td>
                    <td class='text-center w-130px'>
                        <?php
                        if(empty($c->extra)){
                            $extra = [];
                        }else{
                            if(is_numeric($c->extra)){
                                $extra = $c->extra;
                            }else{
                                $extra = json_decode($c->extra, true);
                            }
                        }

                        $flag = empty($c->extra) ? '' :($c->extra == '1' ? '(需要修改)' : '(无需修改)');
                        if(empty($c->before)) {
                            echo zget($allstatus, $c->after, '-') ;
                        }elseif(in_array($c->after, array('suspend', 'renew')) ) {
                            $after = $c->after;
                            $afterOp = $after. 'Action';
                            echo $lang->review->$afterOp;
                        }elseif($c->before == 'baseline') {
                            if(in_array($c->after, array('yes', 'no'))){
                                echo zget($lang->review->condition, $c->after);
                            }else{
                                if(isset($extra['isReject']) && $extra['isReject'] == 2){
                                    $baseLineCondition = $extra['baseLineCondition'];
                                    echo zget($lang->review->condition, $baseLineCondition);
                                }else{
                                    echo $allstatus[$c->after];
                                }
                            }
                        }elseif(in_array($c->after, $lang->review->closeStatusList)) {
                            //baseline状态展示位评审通过
                            echo zget($lang->review->closeReasonList, $c->after);
                        } elseif(in_array($c->before, $lang->review->reviewBeforeStatusList) && strpos($c->after,'reject') === false && $c->after != 'updateFiles'){
                            if($c->before == 'waitFormalOwnerReview' && $c->after == 'waitMeetingReview'){
                                echo '会议评审';
                            }else{
                                if(is_array($extra) && isset($extra['appointUser']) && !empty($extra['appointUser'])){
                                    echo '已委托&nbsp;'.zget($users, $extra['appointUser']);
                                }else{
                                    echo '通过'.$flag ;
                                }

                            }
                        } elseif(in_array($c->before, $lang->review->assignBeforeStatusList)  && strpos($c->after,'drop') === false && $c->after != 'updateFiles') {
                            if($c->before == $c->after){
                                echo '已委托' ;
                            }else{
                                echo '已指派' ;
                            }
                        }  elseif($c->before == $c->after) {
                            echo $allstatus[$c->after];
                        } else{
                            echo $allstatus[$c->after];
                        }
                        ?>
                    </td>

                </tr>
            <?php endforeach;?>
            </tbody>
        </table>
    </div>
</div>