<?php include '../../common/view/header.html.php';?>
<?php if(isset($config->maxVersion) or isset($config->proVersion) or isset($config->bizVersion)):?>
<style>#mainContent > .side-col.col-lg{width: 235px}</style>
<style>.hide-sidebar #sidebar{width: 0 !important}</style>
<?php endif;?>
<div id='mainContent' class='main-row'>
  <div class='side-col col-lg' id='sidebar'>
    <?php include 'blockreportlist.html.php';?>
  </div>
  <div class='main-col'>
    <div class='cell'>
      <div class='panel'>
        <div class="panel-heading">
          <div class="panel-title">
            <div class="table-row" id='conditions'>
              <div class="col-xs"><?php echo $title;?></div>
              <div class='col'>
                <div class="inline-block" style="padding-left: 12px;">
                  <?php echo html::submitButton($lang->report->query, 'onClick="executeConditions()"', 'btn btn-primary');?>
                </div>
              </div>
            </div>
          </div>
          <nav class="panel-actions btn-toolbar"></nav>
        </div>
        <div data-ride='table'>
          <?php if(empty($assigns)):?>
          <div class="table-empty-tip">
            <p><span class="text-muted"><?php echo $lang->error->noData;?></span></p>
          </div>
          <?php else:?>
          <table class='table table-condensed table-striped table-bordered table-fixed no-margin' id='bugAssign'>
            <thead>
              <tr class='colhead text-center'>
                <th><?php echo $lang->report->user;?></th>
                <th><?php echo $lang->report->product;?></th>
                <th><?php echo $lang->report->bugTotal;?></th>
                <th><?php echo $lang->report->total;?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($assigns as $account => $assign):?>
              <?php if(!array_key_exists($account, $users)) continue;?>
              <tr class="a-center text-center">
                <td rowspan="<?php echo count($assign['bug']);?>"><?php echo $users[$account];?></td>
                <?php $id = 1;?>
                <?php foreach($assign['bug'] as $product => $count):?>
                <?php if($id != 1) echo '<tr class="a-center text-center">';?>
                <td><?php echo html::a($this->createLink('product', 'view', "product={$count['productID']}"), $product);?></td>
                <td><?php echo $count['count'];?></td>
                <?php if($id == 1):?>
                <td rowspan="<?php echo count($assign['bug']);?>">
                    <?php echo $assign['total']['count'];?>
                </td>
                <?php endif;?>
                <?php if($id != 1) echo '</tr>'; $id ++;?>
                <?php endforeach;?>
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
<?php include '../../common/view/footer.html.php';?>
