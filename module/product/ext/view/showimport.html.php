<?php include '../../../common/view/header.html.php';?>
<?php include '../../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content">
  <div class="main-header">
    <h2><?php echo $lang->product->import;?></h2>
  </div>
  <form target='hiddenwin' method='post'>
    <table class='table table-form' id='showData'>
      <thead>
        <tr>
          <th class='w-50px'><?php echo $lang->lineNumber?></th>
          <th class='w-140px required'><?php echo $lang->product->app?></th>
          <th class='required'><?php echo $lang->product->name?></th>
          <th class='w-140px required'><?php echo $lang->product->line?></th>
          <th class='w-120px required'><?php echo $lang->product->code?></th>
          <th class='w-120px required'><?php echo $lang->product->enableTime?></th>
          <th class='w-140px required'><?php echo $lang->product->comment?></th>
          <th class='w-120px'><?php echo  $lang->product->PO?></th>
          <th class='w-120px'><?php echo $lang->product->desc?></th>

          <th class='w-120px required' style="display:none;"><?php echo $lang->product->type?></th>
          <th class='w-120px required'><?php echo $lang->product->acl?></th>
        </tr>
      </thead>
      <tbody>
        <?php
        $insert = true;
        $addID  = 1;
        ?>
        <?php foreach($rows as $key => $product):?>
        <?php if(empty($product->name)) continue;?>
        <tr valign='top' class='text-left'>
          <td><?php echo $addID;?></td>
          <td><?php echo html::select("apps[$addID]", $apps, $product->app, "class='form-control chosen'")?></td>
          <td><?php echo html::input("names[$addID]", htmlspecialchars($product->name, ENT_QUOTES), "class='form-control'")?></td>
          <td><?php echo html::select("lines[$addID]", $lines, $product->line, "class='form-control chosen'")?></td>
          <td><?php echo html::input("codes[$addID]", htmlspecialchars($product->code, ENT_QUOTES), "class='form-control'")?></td>
          <td>
              <?php echo html::input("enableTime[$addID]", $product->enableTime, "class='form-control form-datetime'");?>
          </td>
          <td>
              <?php echo html::input("comment[$addID]", $product->comment, "class='form-control input-product-comment'");?>
          </td>
          <td><?php echo html::select("pos[$addID]", $users, $product->PO, "class='form-control chosen'")?></td>
          <td><?php echo html::textarea("descs[$addID]", isset($product->desc) ? htmlspecialchars($product->desc) : "", "class='form-control'")?></td>

          <td style="display:none;"><?php echo html::select("types[$addID]", $lang->product->typeList, 'normal', "class='form-control chosen'")?></td>

          <td><?php echo html::select("acls[$addID]", $lang->product->aclList, $product->acl, "class='form-control chosen'")?></td>
          <?php $addID++;?>
        </tr>
        <?php endforeach;?>
      </tbody>
      <tfoot>
        <tr>
          <td colspan='8' class='text-center form-actions'>
          <?php echo html::submitButton($this->lang->save);?>
          </td>
        </tr>
      </tfoot>
    </table>
  </form>
</div>
<script>
$(function(){$.fixedTableHead('#showData');});
</script>
<?php include '../../../common/view/footer.html.php';?>
