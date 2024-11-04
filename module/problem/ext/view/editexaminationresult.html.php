<?php include '../../../common/view/header.html.php';?>
<div id='mainContent' class='main-content'>
  <div class='center-block'>
    <div class='main-header'>
      <h2>
        <span class='label label-id'><?php echo $problem->code;?></span>
        <?php echo isonlybody() ? ("<span title='$problem->code'>" . $lang->problem->editExaminationResult . '</span>') : html::a($this->createLink('problem', 'view', "problemID=$problem->id"), $problem->name);?>
        <?php if(!isonlybody()):?>
        <small><?php echo $lang->arrow . $lang->problem->editExaminationResult;?></small>
        <?php endif;?>
      </h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post'>
      <table class='table table-form'>
          <tr>
              <th><?php echo $lang->problem->examinationResultFlag;?></th>
              <td ><?php echo html::select('examinationResultFlag', $this->lang->problem->examinationResultFlagList, $problem->examinationResultFlag,"class='form-control chosen'");?></td>
          </tr>
          <tr>
              <th><?php echo $lang->problem->examinationResult ;?></th>
              <td ><?php echo html::select('examinationResult', $this->lang->problem->examinationResultList, $problem->examinationResult ,"class='form-control chosen'");?></td>
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
