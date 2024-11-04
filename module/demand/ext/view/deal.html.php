<?php include '../../../common/view/header.html.php';?>
<?php include '../../../common/view/datepicker.html.php';?>
<?php include '../../../common/view/kindeditor.html.php';?>
<style>
.task-toggle{line-height: 28px; color: #0c60e1; cursor:pointer;}
.task-toggle .icon{display: inline-block; transform: rotate(90deg);}
.more-tips{display: none;}
.close-tips{display: none}
</style>
<div id="mainContent" class="main-content fade">
    <?php if($demand->changeLock == 2):?>
        <h2 style="color:red;text-align: center;margin-top:-3%;letter-spacing:5px;"><?php echo $this->lang->demand->changeIng;?></h2>
    <?php else:?>
        <?php if(empty($cantDeal)):?>
    <div class="center-block">
          <div class="main-header">
      <h2><?php echo $lang->demand->deal;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <tr class="hidden">
            <th class='w-140px'><?php echo $lang->demand->handler;?></th>
              <?php echo html::input('user', $this->app->user->account, "class='form-control hidden'");?>
          </tr>
          <tr class="hidden">
            <th class='w-140px'><?php echo $lang->demand->dealStatus;?></th>
              <?php echo html::input('status', 'feedbacked', "class='form-control hidden'");?>
          </tr>
              <th><?php echo $lang->demand->progress;?></th>
              <td colspan='2'><?php echo html::textarea('progress', '', "class='form-control'");?></td>
          </tr>
          <tr>
            <td class='form-actions text-center' colspan='3'><?php echo html::submitButton($this->lang->demand->submitBtn) . html::backButton();?></td>
          </tr>
        </tbody>
      </table>
    </form>
    <?php else:?>
      <h2 style="color:red;text-align: center;margin-top:-3%;letter-spacing:5px;"><?php echo $cantDeal;?></h2>
    <?php endif;?>
    <?php endif;?>
  </div>
</div>
<?php
echo js::set('createPlanTips', $lang->demand->createPlanTips);
echo js::set('productPlan', $demand->productPlan);
echo js::set('product', $demand->product);
echo js::set('execution',$demand->execution ? $demand->execution : '' );
echo js::set('status',$demand->status  );
echo js::set('project',$demand->project ? $demand->project : '' );
echo js::set('fixtype',$demand->fixType  );
?>
<?php include '../../../common/view/footer.html.php';?>
