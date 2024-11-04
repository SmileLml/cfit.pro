<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id='mainContent' class='main-content'>
  <div class='center-block'>
    <div class='main-header'>
      <h2>
        <span class='label label-id'><?php echo $opinion->code;?></span>
        <?php echo isonlybody() ? ("<span title='$opinion->code'>" . $lang->opinion->secureStatus . '</span>') : html::a($this->createLink('opinion', 'view', "opinionID=$opinion->id"), $opinion->name);?>
        <?php if(!isonlybody()):?>
        <small><?php echo $lang->arrow . $lang->opinion->secureStatus;?></small>
        <?php endif;?>
      </h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post'>
      <table class='table table-form'>
          <tr>
              <th><?php echo $lang->opinion->unlockSeparate;?></th>
              <td >
                  <?php echo html::radio('secureStatusLinkage', $this->lang->opinion->unlockSeparateList,1);?>
              </td>
          </tr>
          <th><?php echo $lang->opinion->comment;?></th>
          <td ><?php echo html::textarea('comment', '', "rows='8' class='form-control'");?></td>
        </tr>
        <tr>
          <td colspan='2' class='text-center form-actions'>
            <?php echo html::submitButton($this->lang->opinion->submitBtn);?>
          </td>
        </tr>
      </table>
    </form>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
