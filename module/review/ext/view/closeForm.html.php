<div id='mainContent' class='main-content'>
  <div class='center-block'>
    <div class='main-header'>
      <h2>
        <span class='label label-id'><?php echo $review->id;?></span>
        <?php echo isonlybody() ? ("<span title='$review->id'>" . $lang->review->close . '</span>') : '';?>
        <?php if(!isonlybody()):?>
        <small><?php echo $lang->arrow . $lang->review->close;?></small>
        <?php endif;?>
      </h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' >
      <table class='table table-form'>
          <tr>
              <th class='w-120px'><?php echo $lang->review->closeReason;?></th>
              <td class='w-p45-f'><?php echo html::select('status', $closestatus, '', "class='form-control chosen' required");?></td>
          </tr>

          <tr>
              <th><?php echo $lang->review->addressee;?></th>
              <td>
                  <?php echo html::select('closeMailAccount[]', $users, $mailUsersInfo['mailMainUsers'], "class='form-control chosen' multiple " );?></td>
              <td></td>
          </tr>
          <tr>
              <th class='w-140px'><?php echo $lang->review->mailto;?></th>
              <td colspan="2"><?php echo html::select('mailto[]', $users, $mailUsersInfo['mailCopyUsers'], "class='form-control chosen' multiple");?></td>
          </tr>
        <tr>
          <th><?php echo $lang->comment;?></th>
          <td colspan='2'><?php echo html::textarea('comment', '', "rows='8' class='form-control'");?></td>
        </tr>
        <tr>
          <td colspan='3' class='text-center form-actions'>
            <?php echo html::submitButton();?>
          </td>
        </tr>
      </table>
    </form>
    <hr class='small' />
  </div>
</div>
