<?php include '../../../common/view/header.html.php';?>
<?php include '../../../common/view/kindeditor.html.php';?>
<div id='mainContent' class='main-content'>
  <div class='center-block'>
    <div class='main-header'>
      <h2>
        <span class='label label-id'><?php echo $demand->code;?></span>
        <?php echo isonlybody() ? ("<span title='$demand->code'>" . $lang->demandinside->secureStatus . '</span>') : html::a($this->createLink('demandinside', 'view', "demandID=$demand->id"), $demand->name);?>
        <?php if(!isonlybody()):?>
        <small><?php echo $lang->arrow . $lang->demand->secureStatus;?></small>
        <?php endif;?>
      </h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post'>
      <table class='table table-form'>
          <tr>
              <th><?php echo $lang->demandinside->secureStatusLinkage;?></th>
              <td ><?php echo html::select('secureStatusLinkage', $this->lang->demandinside->secureStatusLinkageList, $demand->secureStatusLinkage,"class='form-control chosen'");?></td>
          </tr>
          <tr>
              <th><?php echo $lang->demandinside->solvedTime;?></th>
              <td ><?php echo html::input('solvedTime', $demand->solvedTime, "class='form-control form-datetime' ");?></td>
          </tr>
          <tr>
              <th><?php echo $lang->demandinside->actualOnlineDate;?></th>
              <td ><?php echo html::input('actualOnlineDate', $demand->actualOnlineDate, "class='form-control form-datetime' ");?></td>
          </tr>
        <tr>
          <th><?php echo $lang->demandinside->comment;?></th>
          <td ><?php echo html::textarea('comment', '', "rows='8' class='form-control'");?></td>
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
<?php include '../../../common/view/footer.html.php';?>
