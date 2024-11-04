<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<style>
    .reviewTip{color: red;}
</style>
<div id="mainContent" class="main-content fade">

          <div class='main-header'>
              <h2>
                  <span class='label label-id'><?php echo $change->code;?></span>
                  <small><?php echo $lang->arrow . $this->lang->change->recallChange;?></small>
              </h2>
          </div>
          <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='changereview'>
              <table class="table table-form">
                  <tbody>
                  <tr >
                      <th><?php echo $lang->change->prompt;?></th>
                      <td colspan='3'>  <div style="color: lightslategray"><span> <?php echo $this->lang->change->changePrompt?></span></div></td>

                  </tr>
                  <tr>
                      <th><?php echo $lang->change->recallCause;?></th>
                      <td colspan='3'><?php echo html::textarea('comment', '', "class='form-control' placeholder=' ".htmlspecialchars($lang->change->commentTip)."' required");?></td>
                  </tr>
                  <tr>
                      <td class='form-actions text-center' colspan='4'>
                          <?php echo html::submitButton() . html::backButton();?>
                      </td>
                  </tr>
                  </tbody>
              </table>
          </form>

  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
