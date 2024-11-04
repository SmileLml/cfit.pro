<?php include '../../../common/view/header.html.php';?>
<?php include '../../../common/view/kindeditor.html.php';?>
<div id='mainContent' class='main-content'>
  <div class='center-block'>
    <div class='main-header'>
      <h2>
        <span class='label label-id'><?php echo $demand->code;?></span>
        <?php echo isonlybody() ? ("<span title='$demand->code'>" . $lang->demand->secureStatus . '</span>') : html::a($this->createLink('demand', 'view', "demandID=$demand->id"), $demand->name);?>
        <?php if(!isonlybody()):?>
        <small><?php echo $lang->arrow . $lang->demand->secureStatus;?></small>
        <?php endif;?>
      </h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post'>
      <table class='table table-form'>
          <tr>
              <th><?php echo $lang->demand->secureStatusLinkage;?></th>
              <td ><?php echo html::select('secureStatusLinkage', $this->lang->demand->secureStatusLinkageList, $demand->secureStatusLinkage,"class='form-control chosen'");?></td>
          </tr>
          <tr>
              <th><?php echo $lang->demand->solvedTime;?></th>
              <td ><?php echo html::input('solvedTime', $demand->solvedTime, "class='form-control form-datetime' ");?></td>
          </tr>
          <tr>
              <th><?php echo $lang->demand->actualOnlineDate;?></th>
              <td ><?php echo html::input('actualOnlineDate', $demand->actualOnlineDate, "class='form-control form-datetime' ");?></td>
          </tr>
        <tr>
          <th><?php echo $lang->demand->comment;?></th>
          <td ><?php echo html::textarea('comment', '', "rows='8' class='form-control'");?></td>
        </tr>
        <tr>
          <td colspan='2' class='text-center form-actions'>
            <?php echo html::submitButton($this->lang->demand->submitBtn);?>
          </td>
        </tr>
      </table>
    </form>
  </div>
</div>
<?php include '../../../common/view/footer.html.php';?>
