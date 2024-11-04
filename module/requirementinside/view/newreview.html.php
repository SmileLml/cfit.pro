<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->requirementinside->review;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <tr>
            <th class='w-110px'><?php echo $lang->requirementinside->name;?></th>
            <td><div class='read-info'><?php echo $requirement->name;?></div></td>
            <td></td>
            <td></td>
          </tr>
          <tr>
            <th><?php echo $lang->requirementinside->dept;?></th>
            <td><?php echo zget($depts, $requirement->dept, '');?></td>
          </tr>
          <tr>
            <th><?php echo $lang->requirementinside->end;?></th>
            <td><?php echo $requirement->end;?></td>
          </tr>
          <tr>
            <th><?php echo $lang->requirementinside->owner;?></th>
            <td><?php echo zget($users, $requirement->owner, '');?></td>
          </tr>
          <tr>
            <th><?php echo $lang->requirementinside->project;?></th>
            <td colspan='2'> <?php echo zget($projects, $requirement->project);?></td>
          </tr>
          <tr>
            <th><?php echo $lang->requirementinside->line;?></th>
            <td colspan='2'>
            <?php
            $lineList = explode(',', str_replace(' ', '', $requirement->line));
            foreach($lineList as $lineID)
            {
                if($lineID) echo ' ' . zget($lines, $lineID, '');
            }
            ?>
            </td>
          </tr>
          <tr>
            <th><?php echo $lang->requirementinside->app;?></th>
                <?php
                $appNames = '';
                $appList = explode(',', $requirement->app);
                foreach ($appList as $app)
                {
                    if($app) $appNames .= ' ' . zget($apps, $app, '');
                }
                ;?>
              <td colspan='2' title='<?php echo $appNames;?>'><?php echo $appNames;?></td>
          </tr>
          <tr>
            <th><?php echo $lang->requirementinside->product;?></th>
            <td colspan='2'>
            <?php
            $productList = explode(',', str_replace(' ', '', $requirement->product));
            foreach($productList as $productID)
            {
                if($productID) echo ' ' . zget($products, $productID, '');
            }
            ?>
            </td>
          </tr>
          <tr>
            <th><?php echo $lang->requirementinside->contact;?></th>
            <td><?php echo $requirement->contact;?></td>
          </tr>
          <tr>
            <th><?php echo $lang->requirementinside->method;?></th>
            <td colspan='2'> <?php echo zget($lang->requirementinside->methodList, $requirement->method);?></td>
          </tr>
          <tr>
            <th><?php echo $lang->requirementinside->desc;?></th>
            <td colspan='2'><div class='read-info'><?php echo $requirement->desc;?></div></td>
          </tr>
          <tr>
            <th><?php echo $lang->requirementinside->analysis;?></th>
            <td colspan='2'><div class='read-info'><?php echo $requirement->analysis;?></div></td>
          </tr>
          <tr>
            <th><?php echo $lang->requirementinside->handling;?></th>
            <td colspan='2'><div class='read-info'><?php echo $requirement->handling;?></div></td>
          </tr>
          <tr>
            <th><?php echo $lang->requirementinside->implement;?></th>
            <td colspan='2'><div class='read-info'><?php echo $requirement->implement;?></div></td>
          </tr>
          <tr>
            <th><?php echo $lang->requirementinside->result;?></th>
            <td><?php echo html::select('result', $lang->requirementinside->resultList, '', "class='form-control chosen'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->requirementinside->mailto;?></th>
            <td colspan='3'><?php echo html::select('mailto[]', $users, $requirement->mailto, "class='form-control chosen' multiple");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->requirementinside->suggestion;?></th>
            <td colspan='3'><?php echo html::textarea('comment', '', "class='form-control'");?></td>
          </tr>
          <tr>
            <td class='form-actions text-center' colspan='4'><?php echo html::submitButton($lang->requirementinside->submit) . html::backButton();?></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
