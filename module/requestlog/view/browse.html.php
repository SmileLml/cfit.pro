<?php include '../../common/view/header.html.php';?>
<div id="mainMenu" class="clearfix">
  <a class="btn btn-link querybox-toggle" id='bysearchTab'><i class="icon icon-search muted"></i> <?php echo $lang->search;?></a>
</div>
<div id="mainContent" class="main-row">
  <div class="main-col">
    <div class="cell<?php if($browseType == 'bysearch') echo ' show';?>" id="queryBox" data-module="requestlog"></div>
    <form class="main-table" data-ride="table" method="post" id="requestForm">
      <table class="table has-sort-head">
        <thead>
          <tr>
            <th class='w-10px'><?php echo $lang->requestlog->id;?></th>
            <th class='w-60px'><?php echo $lang->requestlog->code;?></th>
            <th class='w-100px'><?php echo $lang->requestlog->url;?></th>
            <th class='w-60px'><?php echo $lang->requestlog->objectType;?></th>
            <th class='w-60px'><?php echo $lang->requestlog->purpose;?></th>
            <th class='w-40px'><?php echo $lang->requestlog->requestType;?></th>
            <th class='w-40px'><?php echo $lang->requestlog->status;?></th>
            <th class='w-30px'><?php echo $lang->requestlog->params;?></th>
            <th class='w-30px'><?php echo $lang->requestlog->response;?></th>
            <th class='w-50px'><?php echo $lang->requestlog->requestDate;?></th>
            <th class='w-40px'><?php echo $lang->actions;?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($logList as $log):?>
          <tr>
            <td><?php echo $log->id;?></td>
            <td><?php
                $res = json_decode($log->params);
                if(isset($res->idFromJinke)) {echo $res->idFromJinke;}
                elseif(isset($res->changeOrderId)) {echo $res->changeOrderId;}
                elseif(isset($res->IssueId)) {echo $res->IssueId;}
                else{echo $log->extra;}
                ?></td>
            <td class="text-ellipsis" title="<?php echo $log->url;?>"><?php echo $log->url;?></td>
            <td><?php echo zget($lang->requestlog->objectTypeList, $log->objectType, $log->objectType);?></td>
            <td><?php echo zget($lang->requestlog->purposeList, $log->purpose, $log->purpose);?></td>
            <td><?php echo $log->requestType;?></td>
            <td><?php echo zget($lang->requestlog->statusList, $log->status, $log->status);?></td>
            <td><?php echo html::commonButton($lang->requestlog->details, 'data-type="ajax" data-title="' . $lang->requestlog->params . '" data-remote="' . $this->createLink('requestlog', 'ajaxGetParams', 'id=' . $log->id) . '" data-toggle="modal"', 'btn btn-mini btn-primary triggerButton');?></td>
            <td><?php echo html::commonButton($lang->requestlog->details, 'data-type="ajax" data-title="' . $lang->requestlog->response . '" data-remote="' . $this->createLink('requestlog', 'ajaxGetResponse', 'id=' . $log->id) . '" data-toggle="modal"', 'btn btn-mini btn-primary triggerButton');?></td>
            <td><?php echo $log->requestDate;?></td>
            <td>
                <?php
                    if ($log->purpose == 'pushMobileMsg' && $log->status == 'fail'){
                        echo html::commonButton($lang->requestlog->repush, 'onclick="repushMsg('.$log->id.')"', 'btn-mini btn-primary triggerButton');

                    }else{
                        echo $lang->noData;
                    }
                ?>
            </td>
          </tr>
          <?php endforeach;?>
        </tbody>
      </table>
    </form>
    <div class='table-footer'><?php echo $pager->show('right', 'pagerjs');?></div>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
<script>
    function repushMsg(id) {
        bootbox.confirm('重推前确认单子当前流程状态是否符合预期，确认继续重推？', function (result){
            if((result)){
                var ajaxRepushMsgUrl = '<?php echo $this->createLink('requestlog', 'ajaxRepushMsg')?>'
                $.post(ajaxRepushMsgUrl,{id:id},function (res) {
                    window.location.reload()
                })
            }
        });
    }
</script>
