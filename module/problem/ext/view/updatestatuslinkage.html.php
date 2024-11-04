<?php include '../../../common/view/header.html.php';?>
<?php include '../../../common/view/kindeditor.html.php';?>
<div id='mainContent' class='main-content'>
  <div class='center-block'>
    <div class='main-header'>
      <h2>
        <span class='label label-id'><?php echo $problem->code;?></span>
        <?php echo isonlybody() ? ("<span title='$problem->code'>" . $lang->problem->secureStatus . '</span>') : html::a($this->createLink('problem', 'view', "problemID=$problem->id"), $problem->name);?>
        <?php if(!isonlybody()):?>
        <small><?php echo $lang->arrow . $lang->problem->secureStatus;?></small>
        <?php endif;?>
      </h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post'>
      <table class='table table-form'>
          <tr>
              <th><?php echo $lang->problem->secureStatusLinkage;?></th>
              <td ><?php echo html::select('secureStatusLinkage', $this->lang->problem->secureStatusLinkageList, $problem->secureStatusLinkage,"class='form-control chosen'");?></td>
          </tr>
          <tr>
              <th><?php echo $lang->problem->solveDate;?></th>
              <td ><?php echo html::input('solvedTime', $problem->solvedTime, "class='form-control form-datetime' ");?></td>
          </tr>
          <tr>
              <th><?php echo $lang->problem->actualOnlineDate;?></th>
              <td ><?php echo html::input('actualOnlineDate', $problem->actualOnlineDate, "class='form-control form-datetime' ");?></td>
          </tr>
        <tr>
          <th><?php echo $lang->problem->comment;?></th>
          <td ><?php echo html::textarea('comment', '', "rows='8' class='form-control'");?></td>
        </tr>
        <tr>
          <td colspan='2' class='text-center form-actions'>
            <?php echo html::submitButton();?>
          </td>
        </tr>
      </table>
    </form>
  </div>
</div>
<?php include '../../../common/view/footer.html.php';?>
