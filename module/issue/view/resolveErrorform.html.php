<?php
/**
 * The resolve view of issue module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Congzhi Chen <congzhi@cnezsoft.com>
 * @package     issue
 * @version     $Id$
 * @link        http://www.zentao.net
 */
?>
<tr>
    <th><?php echo $lang->issue->resolution;?></th>
    <td>
        <?php echo html::select('resolution', $lang->issue->resolveMethods, $resolution, 'class="form-control chosen" onchange="getSolutions()"');?>
    </td>
</tr>
<tr>
    <th></th>
    <td><?php echo $lang->issue->resolveMethodError;?></td>
</tr>

