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
  <div class='main-col'>
    <div class='cell'>
      <div class='panel'>
        <div class="panel-heading">
          <div class="panel-title">
          <?php echo $lang->qareport->customBrowse;?>
          </div>
          <?php if(common::hasPriv('qareport', 'custom')):?>
          <nav class="panel-actions btn-toolbar">
            <?php echo html::a($this->createLink('qareport', 'custom'), $lang->crystal->custom, '', "class='btn btn-primary btn-sm'")?>
          </nav>
          <?php endif;?>
        </div>
        <div class='main-table' data-ride='table'>
          <table class='table table-condensed table-striped table-bordered table-fixed no-margin'>
            <thead>
              <tr>
                <th class='w-50px'><?php echo $lang->crystal->id?></th>
                <th width='160'><?php echo $lang->crystal->name?></th>
                <th><?php echo $lang->crystal->desc?></th>
                <th class='w-90px'><?php echo $lang->crystal->module?></th>
                <th class='w-130px'><?php echo $lang->actions?></th>
              </tr>
            </thead>
            <tbody class='text-center'>
              <?php foreach($reports as $report):?>
              <tr>
                <td><?php echo $report->id;?></td>
                <td class='text-left'>
                  <?php
                  $name = json_decode($report->name, true);
                  if(empty($name)) $name[$this->app->getClientLang()] = $report->name;
                  echo zget($name, $this->app->getClientLang(), '');
                  ?>
                </td>
                <?php
                $desc = json_decode($report->desc, true);
                $desc = zget($desc, $this->app->getClientLang(), '');
                ?>
                <td class='text-left' title='<?php echo $desc?>'><?php echo $desc;?></td>
                <td>
                  <?php
                  $modules = explode(',', trim($report->module, ','));
                  foreach($modules as $module) echo $lang->crystal->moduleList[$module] . ' ';
                  ?>
                </td>
                <td>
                  <?php
                  if(common::hasPriv('qareport', 'useReport')) echo html::a(inlink('useReport', "reportID=$report->id"), $lang->report->useReport, '', $report->vars ? "data-type='iframe' data-toggle='modal'" : '');
                  if(common::hasPriv('qareport', 'editReport')) echo html::a(inlink('editReport', "reportID=$report->id"), $lang->report->editReport, '', "data-type='iframe' data-toggle='modal'");
                  if(common::hasPriv('qareport', 'deleteReport')) echo html::a(inlink('deleteReport', "reportID=$report->id"), $lang->delete, 'hiddenwin');
                  ?>
                </td>
              </tr>
              <?php endforeach;?>
            </tbody>
          </table>
          <div class='table-footer'>
            <?php $pager->show('right', 'pagerjs');?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>

</script>
<?php include '../../common/view/footer.html.php';?>
