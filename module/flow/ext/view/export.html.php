<?php
/**
 * The export view file of flow module of RanZhi.
 *
 * @copyright   Copyright 2009-2018 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Gang Liu <liugang@cnezsoft.com>
 * @package     flow
 * @version     $Id$
 * @link        http://www.ranzhi.org
 */
?>
<?php
chdir('../../view');
if(empty($fields))
{
    include '../../common/view/header.lite.html.php';
    if(commonModel::hasPriv('flow.workflowfield', 'setExport'))
    {
        $designLink = baseHTML::a($this->createLink('workflowfield', 'setExport', "module={$module}"), $lang->flow->setExport, "target='_parent'");
    }
    else
    {
        $designLink = $lang->flow->setExport;
    }
    echo "<div class='alert' style='margin-bottom:0px;padding:20px'>" . sprintf($lang->flow->tips->emptyExportFields, $designLink) . '</div>';
}
else
{
    include '../../file/view/export.html.php';
}
?>
<?php js::set('module', 'flow');?>
<script>
$(function()
{
    $('#exportType').closest('tr').hide();
})
</script>
