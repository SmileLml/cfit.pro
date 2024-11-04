<?php include '../../../common/view/header.html.php';?>
<?php include '../../../common/view/datepicker.html.php';?>
<?php include '../../../common/view/kindeditor.html.php';?>
<style>
.task-toggle{line-height: 28px; color: #0c60e1; cursor:pointer;}
.task-toggle .icon{display: inline-block; transform: rotate(90deg);}
.more-tips{display: none;}
.close-tips{display: none}
</style>
<?php if($demand->status == 'wait'):?>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
          <div class="main-header">
      <h2><?php echo $this->lang->demandinside->deal;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <tr class="hidden">
            <th class='w-140px'><?php echo $this->lang->demandinside->handler;?></th>
              <?php echo html::input('user', $this->app->user->account, "class='form-control hidden'");?>
          </tr>
          <tr class="hidden">
            <th class='w-140px'><?php echo $this->lang->demandinside->dealStatus;?></th>
              <?php echo html::input('status', 'feedbacked', "class='form-control hidden'");?>
          </tr>
          <tr>
            <th class='w-140px'><?php echo $this->lang->demandinside->nextUser;?></th>
            <td><?php echo html::select('dealUser', $users, $demand->acceptUser, "class='form-control chosen' ");?></td>
          </tr>
          <tr>
            <th class='w-140px'><?php echo $this->lang->demandinside->mailto;?></th>
            <td colspan="2"><?php echo html::select('mailto[]', $users, '', "class='form-control chosen' multiple");?></td>
          </tr>

            <th><?php echo $this->lang->demandinside->progress;?></th>
            <td colspan='2'><?php echo html::textarea('progress', '', "class='form-control'");?></td>
          </tr>
          <tr>
            <td class='form-actions text-center' colspan='3'><?php echo html::submitButton($this->lang->demandinside->submitBtn) . html::backButton();?></td>
          </tr>
        </tbody>
      </table>
    </form>

  </div>
</div>
<?php else:?>
    <div id='mainContent' class='main-content'>
        <div class='center-block'>
            <div class='main-header'>
                <h2>
                    <?php echo isonlybody() ? ("<span title='$demand->code'>" . $lang->demandinside->deal . '</span>') : html::a($this->createLink('demandinside', 'view', "demandID=$demand->id"), $demand->title);?>
                    <?php if(!isonlybody()):?>
                        <small><?php echo $lang->arrow . $lang->demandinside->secureStatus;?></small>
                    <?php endif;?>
                    <span class='label label-id'><?php echo $demand->code;?></span>

                </h2>
            </div>
            <form class="load-indicator main-form form-ajax" method='post'>
                <table class='table table-form'>
                    <tr>
                        <th><?php echo $lang->demandinside->dealStatus;?></th>
                        <td ><?php echo html::select('status', $this->lang->demandinside->dealStatusList, 'onlinesuccess',"class='form-control chosen'");?></td>
                    </tr>
<!--                    <tr>-->
<!--                        <th>--><?php //echo $lang->demandinside->solvedTime;?><!--</th>-->
<!--                        <td class = 'required'>--><?php //echo html::input('solvedTime', '', "class='form-control form-datetime' ");?><!--</td>-->
<!--                    </tr>-->
                    <tr>
                        <th><?php echo $lang->demandinside->actualOnlineDate;?></th>
                        <td class = 'required'><?php echo html::input('actualOnlineDate', '', "class='form-control form-datetime' ");?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->demandinside->comment;?></th>
                        <td><?php echo html::textarea('comment', '', "rows='8' class='form-control'");?></td>
                    </tr>
                    <tr>
                        <td colspan='2' class='text-center form-actions'>
                            <?php echo html::submitButton($this->lang->demandinside->submitBtn);?>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
<?php endif;?>
<?php include '../../../common/view/footer.html.php';?>
