<?php include '../../common/view/header.html.php';?>
<div id="mainMenu" class="clearfix">
<div id="mainContent" class="main-row fade">
  <div class='side-col'>
    <div class='cell'>
      <div class="detail">
        <div class='detail-title'><?php echo $lang->measrecord->list;?></div>
      </div>
      <div class="detail-content article-content">
        <ul class='tree' id='measTree'>
          <?php
          foreach($measList as $purpose => $meases)
          {
              $class = '';
              $purposeName = zget($lang->measurement->purposeList, $purpose, $purpose);
              echo "<li class='item-purpose'>" . "<a class='tree-toggle'><span class='title' title='{$purposeName}'>" . $purposeName. '</span></a>';
              if(!empty($meases))
              {
                  echo "<ul>";
                  foreach($meases as $meas)
                  {
                      $class = '';
                      if(isset($measurement) and zget($measurement, 'id') == $meas->mid) $class = 'selected';
                      echo '<li class="item-meas">' . html::a($this->createLink('measrecord', 'browse', "program={$program}&measurement={$meas->mid}"), '<i class="icon icon-file-text"></i> ' . $meas->name, '', "class='$class' title='$meas->name'") . "</li>";
                  }
                  echo "</ul>";
              }
              echo '</li>';
          }
          ?>
        </ul>
      </div>
    </div>
  </div>
  <div class="main-col col-8">
    <div class="container">
      <?php if(empty($records)):?>
      <div class="table-empty-tip">
        <p><span class="text-muted"><?php echo $lang->noData;?></span></p>
      </div>
      <?php else:?>
      <div class='main-table'>
        <table class='table table-bordered'>
          <thead>
            <tr>
              <th class='w-60px'><?php echo $lang->idAB;?></th>
              <th><?php echo $lang->measurement->name;?></th>
              <?php foreach($measurement->params as $param):?>
              <th class='text-center'><?php echo $param->showName;?></th>
              <?php endforeach;?>
              <th class='text-center'>
                <?php echo $lang->measrecord->value;?>
                (<?php echo $measurement->unit?>)
              </th>
              <th class='w-120px text-center'><?php echo $lang->measrecord->date;?></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($records as $record):?>
            <?php $record->params = json_decode($record->params);?>
            <tr>
              <td><?php echo $record->id;?></td>
              <td><?php echo $measurement->name;?></td>
              <?php foreach($measurement->params as $param):?>
              <td class='text-center'>
                <?php if(strpos($param->varName, 'program') !== false) echo zget($programs, zget($record->params, $param->varName, ''), '')?>
                <?php if(strpos($param->varName, 'product') !== false) echo zget($products, zget($record->params, $param->varName, ''), '')?>
                <?php if(strpos($param->varName, 'project') !== false) echo zget($projects, zget($record->params, $param->varName, ''), '')?>
              </td>
              <?php endforeach;?>
              <td class='text-right'><?php echo $record->value;?></td>
              <td class='text-center'><?php echo $record->date;?></td>
            </tr>
            <?php endforeach;?>
          </tbody>
        </table>
        <div class='table-footer'>
        </div>
      </div>
      <?php endif;?>
    </div>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
