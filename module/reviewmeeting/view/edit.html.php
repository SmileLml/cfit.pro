<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<style>
    .input-group-addon{min-width: 150px;}
    .input-group{margin-bottom: 2px;}
    .container{witdh:1200px;}
</style>
<div id='mainContent' class='main-content fade in  scrollbar-hover' style="min-height:450px; max-height: 500px; ">
  <div class='center-block'>
    <div class='main-header'>
      <h2>
        <span class='label label-id'><?php echo $meetingInfo->id;?></span>
        <span><?php echo $meetingInfo->meetingCode;?></span>
        <small><?php echo $lang->arrow . $lang->reviewmeeting->edit ;?></small>
      </h2>
    </div>
      <?php if(!$checkRes['result']):?>
          <div class="tipMsg">
              <span><?php echo $checkRes['message']; ?></span>
          </div>
            <?php else:?>
          <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
              <table class='table table-form'>
                  <tr>
                      <th class='w-100px'><?php echo $lang->reviewmeeting->reviewer;?></th>
                      <td>
                          <?php echo html::select("reviewer", $users, $meetingInfo->reviewer, "class='form-control chosen' required");?>
                      </td>
                      <td colspan="2">
                          <div class='input-group'>
                              <span class='input-group-addon'><?php echo $lang->reviewmeeting->owner;?></span>
                              <?php echo html::select("owner", $users, $meetingInfo->owner, "class='form-control chosen' required");?>
                          </div>
                      </td>
                      <td colspan="2">
                          <div class='input-group'>
                              <span class='input-group-addon'> <?php echo $lang->reviewmeeting->meetingPlanTime;?></span>
                              <?php echo html::input('meetingPlanTime',  $meetingInfo->meetingPlanTime, "class='form-control form-datetime' required");?>
                          </div>
                      </td>
                  </tr>
                  <tr>
                      <th> <?php echo  $lang->reviewmeeting->reviewTitle;?></th>
                      <td colspan="5" id="reviewIds">
                          <?php echo html::select("reviewIds[]", $reviewList, $reviewIds ? implode(',', $reviewIds): '', "  class='form-control chosen'  multiple");?>
                      </td>
                  </tr>
                  <tr>
                      <th> <?php echo $lang->reviewmeeting->meetingPlanExport;?></th>
                      <td colspan="5">
                          <?php echo html::select("meetingPlanExport[]", $users, $meetingInfo->meetingPlanExport, "class='form-control chosen' required multiple");?>
                      </td>
                  </tr>
                  <tr>
                      <th><?php echo $lang->comment ;?></th>
                      <td colspan='5'>
                          <?php echo html::textarea('comment', '', "class='form-control'");?>
                      </td>
                  </tr>
                  <tr>
                      <td class='text-center' colspan='6'>
                          <?php echo html::submitButton();?>
                      </td>
                  </tr>
              </table>
          </form>

      <?php endif;?>
  </div>
</div>
<?php
js::set('meetingId', $meetingInfo->id);
js::set('status', $meetingInfo->status);
?>
<?php include '../../common/view/footer.html.php';?>

