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
<?php js::set('type', $type);?>
<div id="main-content" class='main-content'>
  <!-- SQL查询 -->
  <div class='center-block'><?php include 'blocksql.html.php'?></div>
  <?php if(!empty($dataList)):?>
  <!-- 报表条件 -->
  <div class='center-block'><?php include 'blockcondition.html.php'?></div>
  <?php endif;?>
  <?php if(!empty($dataList)):?>
  <!-- 显示结果 -->
  <div class='center-block result'><?php include 'blockdata.html.php'?></div>
  <?php else:?>
  <div class='center-block result'>
    <div class='panel'>
      <div class='panel-heading'>
        <div class='panel-title'><?php echo $lang->crystal->result?></div>
      </div>
      <div class='panel-body'><?php echo $lang->error->noData?></div>
    </div>
  </div>
  <?php endif;?>
</div>

<script>

</script>
<?php include '../../common/view/footer.html.php';?>
