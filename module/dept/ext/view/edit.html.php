<?php
/**
 * The edit view of dept module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Yidong Wang <yidong@cnezsoft.com>
 * @package     dept
 * @version     $Id: edit.html.php 4795 2013-06-04 05:59:58Z zhujinyonging@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php
$webRoot = $this->app->getWebRoot();
$jsRoot = $webRoot . "js/";
if (isset($pageCSS)) css::internal($pageCSS);
?>
<div class='modal-dialog w-500px'>
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><i class="icon icon-close"></i></button>
        <h4 class="modal-title"><strong><?php echo $lang->dept->edit; ?></strong></h4>
    </div>
    <div class='modal-body'>
        <form action="<?php echo inlink('edit', 'deptID=' . $dept->id); ?>" target='hiddenwin' method='post'
              class='mt-10px' id='dataform'>
            <table class='table table-form' style='width:100%'>
                <tr>
                    <th class='w-120px'><?php echo $lang->dept->parent; ?></th>
                    <td><?php echo html::select('parent', $optionMenu, $dept->parent, "class='form-control chosen'"); ?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->dept->name; ?></th>
                    <td><?php echo html::input('name', $dept->name, "class='form-control'"); ?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->dept->ldapName; ?></th>
                    <td><?php echo html::input('ldapName', $dept->ldapName, "class='form-control'"); ?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->dept->manager1; ?></th>
                    <td><?php echo html::select('manager1', $users, $dept->manager1, "class='form-control chosen'"); ?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->dept->manager; ?></th>
                    <td><?php echo html::select('manager[]', $users, $dept->manager, "class='form-control chosen' multiple"); ?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->dept->leader1; ?></th>
                    <td><?php echo html::select('leader1', $users, $dept->leader1, "class='form-control chosen'"); ?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->dept->leader; ?></th>
                    <td><?php echo html::select('leader[]', $users, $dept->leader, "class='form-control chosen' multiple"); ?></td>
                </tr>
                <!--20220214 新增组长节点-->
                <tr>
                    <th><?php echo $lang->dept->groupleader; ?></th>
                    <td><?php echo html::select('groupleader[]', $users, $dept->groupleader, "class='form-control chosen' multiple"); ?></td>
                </tr>

                <!--20220408  wangjiurong 新增审核接口人-->
                <tr>
                    <th><?php echo $lang->dept->firstReviewer; ?></th>
                    <td><?php echo html::select('firstReviewer[]', $users, $dept->firstReviewer, "class='form-control chosen'"); ?></td>
                </tr>
                <!--20220408  wangjiurong 新增审核专员-->
                <tr>
                    <th><?php echo $lang->dept->reviewer; ?></th>
                    <td><?php echo html::select('reviewer[]', $users, $dept->reviewer, "class='form-control chosen'"); ?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->dept->executive; ?></th>
                    <td><?php echo html::select('executive[]', $users, $dept->executive, "class='form-control chosen' multiple"); ?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->dept->cm; ?></th>
                    <td><?php echo html::select('cm[]', $users, $dept->cm, "class='form-control chosen' multiple"); ?></td>
                </tr>
                <!--20220310 新增qa-->
                <tr>
                    <th><?php echo $lang->dept->qa; ?></th>
                    <td><?php echo html::select('qa[]', $users, $dept->qa, "class='form-control chosen' multiple"); ?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->dept->po; ?></th>
                    <td><?php echo html::select('po[]', $users, $dept->po, "class='form-control chosen' multiple"); ?></td>
                </tr>
                <!--20221030 新增测试负责人-->
                <tr>
                    <th><?php echo $lang->dept->testLeader; ?></th>
                    <td><?php echo html::select('testLeader[]', $users, $dept->testLeader, "class='form-control chosen' multiple"); ?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->dept->planPerson; ?></th>
                    <td <?php if($deptID == 2):?> class="required" <?php endif;?>><?php echo html::select('planPerson', $users, $dept->planPerson, "class='form-control chosen'"); ?></td>
                </tr>
                <tr>
                    <td colspan='2' class='text-center'>
                        <?php echo html::submitButton(); ?>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>
<script>
    <?php if (isset($pageJS)) echo $pageJS;?>
    $('#dataform .chosen').chosen();
    $(document).ready(function () {
        $("#dataform .picker-select[data-pickertype!='remote']").picker({chosenMode: true});
        $("#dataform [data-pickertype='remote']").each(function () {
            var pickerremote = $(this).attr('data-pickerremote');
            $(this).picker({chosenMode: true, remote: pickerremote});
        })
    });
</script>
