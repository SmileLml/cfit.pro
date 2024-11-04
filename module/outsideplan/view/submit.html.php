<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade" style="min-height: 350px;">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->projectplan->submit;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <tr>
            <th class='w-110px'><?php echo $lang->projectplan->submitBy . '/' . $lang->projectplan->dept;?></th>
            <td colspan='3'>
            <?php echo rtrim($this->app->user->realname . ' ' . zget($depts, $this->app->user->dept, ''), '/');?>
            </td>
          </tr>
          <tr>
            <th><?php echo $lang->projectplan->depts;?></th>
            <td colspan='3'>
            <?php
            foreach($depts as $dept)
            {
                echo "<div class='checkbox-primary'><input type='checkbox' name='depts[]' value='$dept->id'>";
                echo "<label>$dept->name<span class='review-name'>" . zget($users, $dept->manager, '') . "</span></label>";
                echo "</div>";
            }
            ?>
            </td>
          </tr>
          <tr>
            <td style="padding-top: 50px;" class='form-actions text-center' colspan='4'><?php echo html::submitButton();?></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<style>
.review-name {padding-left: 10px; color: #585858;}
</style>
<?php include '../../common/view/footer.html.php';?>
