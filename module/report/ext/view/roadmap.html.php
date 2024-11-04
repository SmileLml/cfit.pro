<?php include '../../../common/view/header.html.php';?>
<style>
#mainContent > .side-col.col-lg{width: 235px}
.hide-sidebar #sidebar{width: 0 !important}
</style>
<div id='mainContent' class='main-row'>
  <div class='side-col col-lg' id='sidebar'>
    <?php include '../../view/blockreportlist.html.php';?>
  </div>
  <div class='main-col'>
    <div class='cell'>
      <div class='panel'>
        <div class="panel-heading">
          <div class="panel-title">
            <div class="table-row" id='conditions'>
              <div class="col-xs"><?php echo $title;?></div>
              <div class="col-xs text-right text-gray text-middle"><?php echo $lang->report->conditions?></div>
              <div class='col'>
                <div class="checkbox-primary inline-block">
                  <input type="checkbox" name="closedProduct" value="closedProduct" id="closedProduct" <?php if(strpos($conditions, 'closedProduct') !== false) echo "checked='checked'"?> />
                  <label for="closedProduct"><?php echo $lang->report->closedProduct?></label>
                </div>
                <div class="inline-block" style="padding-left: 12px;">
                  <?php echo html::submitButton($lang->report->query, 'onClick="executeConditions()"', 'btn btn-primary');?>
                </div>
              </div>
            </div>
          </div>
          <nav class="panel-actions btn-toolbar"></nav>
        </div>
        <div class='roadmap-wrap' data-ride='table'>
          <?php if(empty($products)):?>
          <div class="table-empty-tip">
            <p><span class="text-muted"><?php echo $lang->error->noData;?></span></p>
          </div>
          <?php else:?>
          <table id="roadmap" class='table table-condensed table-striped table-bordered table-fixed active-disabled'>
            <thead>
              <tr class='colhead'>
                <th class="w-200px"><?php echo $lang->report->product;?></th>
                <th><?php echo $lang->report->plan;?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($products as $productID => $product):?>
              <tr class="text-center">
                <td title="<?php echo $product?>"><?php echo $product?></td>
                <td>
                  <?php if(!empty($plans[$productID])):?>
                  <?php foreach($plans[$productID] as $plan):?>
                  <div class='plan'>
                    <div class='text-ellipsis' title='<?php echo $plan->title;?>' ><?php echo $plan->title?></div>
                    <div><?php echo ($plan->begin == '2030-01-01' and $plan->end == '2030-01-01') ? $lang->report->future : ($plan->begin . ' ~ ' . $plan->end);?></div>
                  </div>
                  <?php endforeach;?>
                  <?php endif;?>
                </td>
              </tr>
              <?php endforeach;?>
            </tbody>
          </table>
          <?php endif;?>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include '../../../common/view/footer.html.php';?>
