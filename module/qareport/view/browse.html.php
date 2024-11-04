<?php
/**
 * The report view file of qareport module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     qareport
 * @version     $Id: report.html.php 4657 2013-04-17 02:01:26Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<div id="mainContent" class='main-row'>
  <div class='side-col col-lg'>
    <div class='panel'>
      <div class='panel-heading'>
        <div class='panel-title'><?php echo $lang->qareport->report->select;?></div>
      </div>
      <div class='cell' style='box-shadow: none;'>
        <?php include './commonheader.html.php'?>
      </div>
    </div>
  </div>
  <div class='main-col'>
    <div class="cell">
      <form action="<?php echo $this->createLink('qareport', 'browse', 'reportType=' . $reportType . '&chartType=' . $chartType);?>" id="qareportForm" method="post">
        <?php if(!in_array($reportType, array('bugsPerExecution', 'bugsPerBuild', 'bugsPerModule'))):?>
        <div class="table-row" id='conditions'>
          <div class='w-220px col-md-3 col-sm-6'>
            <div class='input-group'>
              <span class='input-group-addon text-ellipsis'><?php echo $lang->qareport->startTime;?></span>
              <?php echo html::input('begin', $begin, "class='form-control form-date'");?>
              <span class='input-group-addon text-ellipsis'><?php echo '~';?></span>
              <?php echo html::input('end', $end, "class='form-control form-date'");?>
            </div>
          </div>
          <div class='w-220px col-md-3 col-sm-6'>
          </div>
          <div class='w-220px col-md-3 col-sm-6'>
          </div>
        </div>
        <br />
        <?php endif;?>
        <div class="table-row" id='conditions'>
          <div class='w-220px col-md-3 col-sm-6'>
            <div class='input-group'>
              <span class='input-group-addon text-ellipsis'><?php echo $lang->qareport->application;?></span>
              <?php echo html::select('application', $applicationPairs, $application , "class='form-control picker-select'");?>
            </div>
          </div>
          <div class='w-220px col-md-3 col-sm-6'>
            <div class='input-group'>
              <span class='input-group-addon text-ellipsis'><?php echo $lang->qareport->product;?></span>
              <?php echo html::select('product', $productPairs, $product, "class='form-control picker-select'");?>
            </div>
          </div>
          <div class='w-220px col-md-3 col-sm-6'>
            <div class='input-group'>
              <span class='input-group-addon text-ellipsis'><?php echo $lang->qareport->project;?></span>
              <?php echo html::select('project', $projectPairs, $project, "class='form-control picker-select'");?>
            </div>
          </div>
        </div>
        <div class="table-row text-center form-actions">
          <div class="col-md-3 col-sm-6">
          <?php echo html::submitButton($lang->qareport->query, '', 'btn btn-primary');?>
          <?php echo html::resetButton($lang->qareport->reset, '', 'btn btn-primary');?>
          </div>
        </div>
      </form>
    </div>
    <div class='cell'>
      <div id='chartAction' style='display: flex;'>
        <div class='btn-toolbar' style='flex: 1;'>
          <?php foreach($lang->qareport->typeList as $type => $typeName):?>
          <?php $switchChartUrl = $this->createLink('qareport', 'browse', array('reportType' => $reportType, 'chartType' => $type, 'switch' => 'yes'));?>
          <?php echo html::a($switchChartUrl, ($type == 'default' ? "<i class='icon icon-list-alt muted'></i> " : "<i class='icon icon-chart-{$type} muted'></i>") . $typeName, '', "class='btn btn-link " . ($type == $chartType ? 'btn-active-line' : '') . "'")?>
          <?php endforeach;?>
        </div>
        <div class='btn-toolbar' style='display: flex; flex: 1; flex-direction: row-reverse;'>
        <?php if(common::hasPriv('qareport', 'export')) echo html::a($this->createLink('qareport', 'export', 'module=' . $this->app->getModuleName()), $lang->export, '', "class='btn btn-primary' data-width='600px' id='exportchart'");?>
        </div>
      </div>
      <div class='text-muted' style='padding-top:5px'><?php echo $lang->qareport->report->help;?></div>
      <?php if(empty($datas[$reportType])):?>
      <div class="table-empty-tip">
        <p>
          <span class="text-muted">
            <?php echo $lang->qareport->report->emptyTip;?>
          </span>
        </p>
      </div>
      <?php else:?>
      <?php foreach($charts as $chartType => $chartOption):?>
      <div class='table-row chart-row'>
        <div class='main-col'>
          <div class='chart-wrapper text-center'>
            <h4><?php echo $lang->qareport->report->charts[$chartType];?></h4>
            <div class='chart-canvas'><canvas id='chart-<?php echo $chartType ?>' width='<?php echo $chartOption->width;?>' height='<?php echo $chartOption->height;?>' data-responsive='true'></canvas></div>
          </div>
        </div>
        <div class='side-col col-xl'>
          <div style="overflow:auto;" class='table-wrapper'>
            <table class='table table-condensed table-hover table-striped table-bordered table-chart' data-chart='<?php echo $chartOption->type;?>' data-target='#chart-<?php echo $chartType?>' data-animation='false'>
              <thead>
                <tr>
                  <th class='chart-label' colspan='2'><?php echo $lang->qareport->item;?></th>
                  <th class='w-50px text-right'><?php echo $lang->qareport->value;?></th>
                  <th class='w-60px text-right'><?php echo $lang->qareport->percent;?></th>
                </tr>
              </thead>
              <?php foreach($datas[$chartType] as $key => $data):?>
              <tr>
                <td class='chart-color'><i class='chart-color-dot'></i></td>
                <td class='chart-label text-left' title='<?php echo isset($data->title) ? $data->title : $data->name;?>'><?php echo $data->name;?></td>
                <td class='chart-value text-right'><?php echo $data->value;?></td>
                <td class='text-right'><?php echo ($data->percent * 100) . '%';?></td>
              </tr>
              <?php endforeach;?>
            </table>
          </div>
        </div>
      </div>
      <?php endforeach;?>
      <?php endif;?>
    </div>
  </div>
</div>

<?php js::set('exportAppliactionID', $application);?>
<?php js::set('exportProductID', $product);?>
<?php js::set('exportProjectID', $project);?>
<?php js::set('exportBegin', $begin);?>
<?php js::set('exportEnd', $end);?>
<?php js::set('items', $reportType);?>
<?php js::set('queryTip', $lang->qareport->queryTip);?>

<script>
$('#reset').click(function()
{
    $("#application option:selected").removeAttr("selected");
    $('#application').trigger('chosen:updated');

    $("#product option:selected").removeAttr("selected");
    $('#product').trigger('chosen:updated');

    $("#project option:selected").removeAttr("selected");
    $('#project').trigger('chosen:updated');
});

$('#exportchart').modalTrigger();

$('#qareportForm').submit(function()
{
    var application = $('#application').val();
    var product     = $('#product').val();
    var project     = $('#project').val();
    if(!application && !product && !project)
    {
        alert(queryTip);
        return false;
    }
})
</script>
<?php include '../../common/view/footer.html.php';?>
