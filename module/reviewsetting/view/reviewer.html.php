<?php include '../../common/view/header.html.php';?>
<div id='mainMenu' class='clearfix'>
  <div class='pull-left'>
  <?php include '../../reviewcl/view/menu.html.php';?>
  </div>
</div>
<div id="mainContent" class="main-row">
  <div class="side-col col-3">
    <div class="cell">
      <div class="list-group">
      <?php foreach ($objectType as $key => $label):?>
        <?php $key == $object ? $active = 'class="active"' : $active = '';?>
        <?php echo html::a($this->createLink('reviewsetting', 'reviewer', "object=$key"), $label, '', $active);?>
      <?php endforeach;?>
      </div>
    </div>
  </div>
  <div class="main-col col-9">
    <div class="main-content">
      <form class="load-indicator main-form form-ajax" method="post">
        <div class="main-header">
          <div class="heading">
            <strong><?php echo $lang->reviewsetting->setting . zget($objectType, $object) . $lang->reviewsetting->reviewer;?></strong>
          </div>
        </div>
        <table class="table table-form">
          <tbody>
            <tr>
              <th class='w-100px'><?php echo $lang->reviewsetting->reviewerRole;?> </th>
              <td><?php echo html::select('role[]', $lang->user->roleList, $roleList, "class='form-control chosen' multiple")?> </td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td></td>
              <td class='form-action text-left'><?php echo html::submitButton();?></td>
            </tr>
          </tbody>
        </table>
      </form>
    </div>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
