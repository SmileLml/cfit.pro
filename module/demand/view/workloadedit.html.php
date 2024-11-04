<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->demand->workloadEdit;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
          <tbody>
          <tr>
            <th class='w-140px'><?php echo $lang->demand->nodeUser;?></th>
            <td><?php echo html::select('account', $users, $consumed->account, "class='form-control chosen'");?></td>
          </tr>
          <tr>
            <th class='w-140px'><?php echo $lang->demand->consumed;?></th>
            <td><?php echo html::input('consumed', $consumed->consumed, "class='form-control'");?></td>
          </tr>

          <?php
            $details = $consumed->details;
            $relevantUserCount = count($details);
            if($relevantUserCount == 0):
            ?>
          <tr id='relevantDept1'>
              <th class='w-140px'><?php echo $lang->demand->relevantDept;?></th>
              <td>
                  <div class='table-row'>
                      <div class='table-col'>
                          <?php echo html::select('relevantUser[]', $users, '', "class='form-control chosen'");?>
                      </div>
                      <div class='table-col'>
                          <div class='input-group'>
                              <span class='input-group-addon fix-border'><?php echo $lang->demand->workload;?></span>
                              <?php echo html::input('workload[]', '', "class='form-control'");?>
                          </div>
                      </div>
                  </div>
              </td>
              <td class="c-actions">
                  <a href="javascript:void(0)" onclick="addRelevantItem(this)" data-id='1' class="btn btn-link"><i class="icon-plus"></i></a>
              </td>
          </tr>
          <?php else:?>
           <?php
                foreach($details as $key => $workload):
                    $indexKey = $key+1;
            ?>
                    <tr id='relevantDept<?php echo $indexKey;?>'>
                        <th class='w-140px'><?php echo $lang->demand->relevantDept;?></th>
                        <td>
                            <div class='table-row'>
                                <div class='table-col'>
                                    <?php echo html::select('relevantUser[]', $users, $workload->account, "class='form-control chosen'");?>
                                </div>
                                <div class='table-col'>
                                    <div class='input-group'>
                                        <span class='input-group-addon fix-border'><?php echo $lang->demand->workload;?></span>
                                        <?php echo html::input('workload[]', $workload->workload, "class='form-control'");?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="c-actions">
                            <a href="javascript:void(0)" onclick="addRelevantItem(this)" data-id='<?php echo $indexKey;?>' class="btn btn-link"><i class="icon-plus"></i></a>
                            <?php if($indexKey > 1):?>
                                <a href="javascript:void(0)" onclick="delRelevantItem(this)" data-id='<?php echo $indexKey;?>' id='codeClose<?php echo $indexKey;?>' class="btn btn-link"><i class="icon-close"></i></a>
                            <?php endif; ?>
                        </td>
                    </tr>
            <?php
                endforeach;
          endif;
          ?>
          <tr>
            <th class='w-140px'><?php echo $lang->demand->before;?></th>
            <td><?php echo html::select('before', $lang->demand->statusList, $consumed->before, "class='form-control chosen'");?></td>
          </tr>
          <tr>
            <th class='w-140px'><?php echo $lang->demand->after;?></th>
            <td><?php echo html::select('after', $lang->demand->statusList, $consumed->after, "class='form-control chosen'");?></td>
          </tr>

          <?php if($isLastConsumed):?>
          <tr>
              <th class='w-140px'><?php echo $lang->demand->nextUser;?></th>
              <td><?php echo html::select('dealUser', $users, $demand->dealUser, "class='form-control chosen'");?></td>
          </tr>
          <?php endif;?>
          
          <tr>
              <th><?php echo $lang->comment;?></th>
              <td colspan='2'><?php echo html::textarea('comment', '', "rows='6' class='form-control'");?></td>
          </tr>
          <tr>
            <td class='form-actions text-center' colspan='3'><?php echo html::submitButton($this->lang->demand->submitBtn) . html::backButton();?></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>

<table class='hidden'>
    <tbody id="relevantDeptTable">
    <tr id='relevantDept0'>
        <th class='w-140px'><?php echo $lang->demand->relevantDept;?></th>
        <td>
            <div class='table-row'>
                <div class='table-col'>
                    <?php echo html::select('relevantUser[]', $users, '', "class='form-control' id='relevantUser0'");?>
                </div>
                <div class='table-col'>
                    <div class='input-group'>
                        <span class='input-group-addon fix-border'><?php echo $lang->demand->workload;?></span>
                        <?php echo html::input('workload[]', '', "class='form-control'");?>
                    </div>
                </div>
            </div>
        </td>
        <td class="c-actions">
            <a href="javascript:void(0)" onclick="addRelevantItem(this)" data-id='0' id='codePlus0' class="btn btn-link"><i class="icon-plus"></i></a>
            <a href="javascript:void(0)" onclick="delRelevantItem(this)" data-id='0' id='codeClose0' class="btn btn-link"><i class="icon-close"></i></a>
        </td>
    </tr>
    </tbody>
</table>
<?php include '../../common/view/footer.html.php';?>
<?php js::set('relevantIndex', $relevantUserCount > 0 ? $relevantUserCount: 1);?>
<script>
    function addRelevantItem(obj)
    {
        var relevantObj  = $('#relevantDeptTable');
        var relevantHtml = relevantObj.clone();
        relevantIndex++;

        relevantHtml.find('#codePlus0').attr({'id':'codePlus' + relevantIndex, 'data-id': relevantIndex});
        relevantHtml.find('#codeClose0').attr({'id':'codeClose' + relevantIndex, 'data-id': relevantIndex});

        relevantHtml.find('#relevantUser0').attr({'id':'relevantUser' + relevantIndex});
        relevantHtml.find('#relevantDept0').attr({'id':'relevantDept' + relevantIndex});

        var objIndex = $(obj).attr('data-id');
        $('#relevantDept' + objIndex).after(relevantHtml.html());

        $('#relevantUser' + relevantIndex).attr('class','form-control chosen');
        $('#relevantUser' + relevantIndex).chosen();

        console.log(relevantHtml.html());
    }

    function delRelevantItem(obj)
    {
        var objIndex = $(obj).attr('data-id');
        $('#relevantDept' + objIndex).remove();
    }
</script>
