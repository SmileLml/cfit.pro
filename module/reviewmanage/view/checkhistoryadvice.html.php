<?php
/**
 * The view file of bug module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     bug
 * @version     $Id: view.html.php 4728 2013-05-03 06:14:34Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<style>
    td p {
        margin-bottom: 0;
    }

    .table-fixed td {
        white-space: unset !important;
    }
</style>
<style class="dialog"></style>
<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<?php if ($this->app->rawModule == 'review'): ?>
    <?php $browseLink = inlink('view', "reviewId=$review->id"); ?>
<?php else: ?>
    <?php $browseLink = $this->session->reviewmanageList ? $this->session->reviewmanageList : inlink('review'); ?>
<?php endif; ?>
<?php include '../../review/ext/view/checkhistoryadviceTable.html.php'; ?>
<?php include '../../common/view/footer.html.php'; ?>
