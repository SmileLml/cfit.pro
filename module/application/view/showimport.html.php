<?php include '../../common/view/header.html.php';?>
<style>
form{overflow-x: scroll}
</style>
<div id="mainContent" class="main-content">
  <div class="main-header clearfix">
    <h2><?php echo $lang->application->import;?></h2>
  </div>
  <form class='main-form' target='hiddenwin' method='post' style='overflow-x:auto'>
    <table class='table  table-form' id='showData'>
      <thead>
        <tr>
          <th class='w-120px'> <?php echo $lang->application->name?></th>
          <th class='w-120px'><?php echo $lang->application->code?></th>
          <th class='w-130px'><?php echo $lang->application->team?></th>
          <th class='w-120px'><?php echo $lang->application->isPayment?></th>
          <th class='w-160px'><?php echo $lang->application->attribute?></th>
          <th class='w-140px'><?php echo $lang->application->network?></th>
          <th class='w-120px'><?php echo $lang->application->fromUnit?></th>
          <th class='w-120px'><?php echo $lang->application->feature?></th>
          <th class='w-120px'><?php echo $lang->application->range?></th>
          <th class='w-140px'><?php echo $lang->application->useDept?></th>
          <th class='w-120px'><?php echo $lang->application->projectMonth?></th>
          <th class='w-140px'><?php echo $lang->application->productDate?></th>
          <th class='w-120px'><?php echo $lang->application->desc?></th>
          <th class='w-120px'><?php echo $lang->application->isBasicLine?></th>
          <th class='w-120px'><?php echo $lang->application->isSyncJinx?></th>
          <th class='w-120px'><?php echo $lang->application->isSyncQz?></th>

        </tr>
      </thead>
      <tbody>
        <?php foreach($appData as $key => $app):?>
        <tr class='text-top'>
          <td><?php echo html::input("name[$key]", $app->name, "class='form-control'")?></td>
          <td><?php echo html::input("code[$key]", $app->code, "class='form-control'")?></td>
          <td><?php echo html::select("team[$key]", $lang->application->teamList, $app->team, "class='form-control'")?></td>
          <td><?php echo html::select("isPayment[$key]", $lang->application->isPaymentList, $app->isPayment, "class='form-control' autocomplete='off'")?></td>
          <td><?php echo html::select("attribute[$key]", $lang->application->attributeList, $app->attribute, "class='form-control text-right' autocomplete='off'")?></td>
          <td><?php echo html::select("network[$key]", $lang->application->networkList, $app->network, "class='form-control text-right' autocomplete='off'")?></td>
          <td><?php echo html::select("fromUnit[$key]", $lang->application->fromUnitList, $app->fromUnit, "class='form-control text-right' autocomplete='off'")?></td>

          <td><?php echo html::input("feature[$key]", $app->feature, "class='form-control'")?></td>
          <td><?php echo html::input("range[$key]", $app->range, "class='form-control'")?></td>
          <td><?php echo html::input("useDept[$key]", $app->useDept, "class='form-control'")?></td>
          <td><?php echo html::input("projectMonth[$key]", $app->projectMonth, "class='form-control'")?></td>
          <td><?php echo html::input("productDate[$key]", !empty($app->productDate) ? $app->productDate :"" , "class='form-control form-date'")?></td>
          <td><?php echo html::input("desc[$key]", $app->desc, "class='form-control'")?></td>
            <td><?php echo html::select("isBasicLine[$key]", $lang->application->boolList, $app->isBasicLine, "class='form-control text-right' autocomplete='off'")?></td>
            <td><?php echo html::select("isSyncJinx[$key]", $lang->application->boolList, $app->isSyncJinx, "class='form-control text-right' autocomplete='off'")?></td>
            <td><?php echo html::select("isSyncQz[$key]", $lang->application->boolList, $app->isSyncQz, "class='form-control text-right' autocomplete='off'")?></td>
        </tr>
        <?php endforeach;?>
      </tbody>
      <tfoot>
        <tr>
          <td colspan='10' class='text-center form-actions'>
            <?php
            echo html::submitButton($this->lang->save);
            echo ' &nbsp; ' . html::backButton();
            ?>
          </td>
        </tr>
      </tfoot>
    </table>
  </form>
</div>
<script>
$(function()
{
    $.fixedTableHead('#showData');
});
</script>
<?php include '../../common/view/footer.html.php';?>
