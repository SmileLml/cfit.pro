<?php
/**
 * The view file of kanban module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2021 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Guangming Sun<sungangming@easycorp.ltd>
 * @package     kanban
 * @version     $Id: view.html.php 935 2021-12-09 10:49:24Z $
 * @link        https://www.zentao.net
 */
?>
<?php include '../../common/view/headerkanban.html.php';?>
<?php include '../../common/view/kanban.html.php';?>

<div id='mainContent' class='main-row'>
    <div class='side-col col-lg' id='sidebar'>
        <?php include './reportmenu.html.php';?>
    </div>
    <div class='main-col'>
        <div class='cell'>
            <div class="with-padding">
                <form method='post' class="searchForm">
                    <div class="table-row" id='conditions'>
                        <div class='w-220px col-md-3 col-sm-6'>
                            <div class='input-group'>
                                <span class='input-group-addon text-ellipsis'><?php echo $lang->kanbanspace->name;?></span>
                                <?php echo html::select('spaceId[]', $spaceData, $spaceId , "class='form-control chosen' onchange='changeSpace()' multiple='multiple'");?>
                            </div>
                        </div>
                        <div class='w-220px col-md-3 col-sm-6'>
                            <div class='input-group'>
                                <?php $kanbansData = !empty($spaceId) ? $kanbans :[];?>
                                <span class='input-group-addon text-ellipsis'><?php echo $lang->kanban->name;?></span>
                                <?php echo html::select('kanbanId[]', $kanbansData, $kanbanId , "class='form-control chosen' multiple='multiple'");?>
                            </div>
                        </div>
                        <div class='w-220px col-md-3 col-sm-4'>
                            <div class='input-group'>
                                <span class='input-group-addon'><?php echo $lang->measure->begin;?></span>
                                <div class='datepicker-wrapper datepicker-date'><?php echo html::input('begin', $begin, "class='form-control form-date'");?></div>
                            </div>
                        </div>
                        <div class='w-220px col-md-3 col-sm-4'>
                            <div class='input-group'>
                                <span class='input-group-addon'><?php echo $lang->measure->end;?></span>
                                <div class='datepicker-wrapper datepicker-date'><?php echo html::input('end', $end, "class='form-control form-date'");?></div>
                            </div>
                        </div>

                        <input type="hidden" name="isParam" value="1">

                    </div>
                    <div class="table-row"  style="margin-top: 10px">
                        <div class='w-220px col-md-3 col-sm-6'>
                            <div class='input-group'>
                                <span class='input-group-addon text-ellipsis'><?php echo $lang->measure->particDepts;?></span>
                                <?php echo html::select('particDepts[]', $depts, $particDepts , "class='form-control chosen' multiple='multiple'");?>
                            </div>
                        </div>
                        <div class='w-220px col-md-3 col-sm-6'>
                            <div class='input-group'>
                                <span class='input-group-addon text-ellipsis'><?php echo $lang->measure->blockMember;?></span>
                                <?php echo html::select('account[]', $participants, $account , "class='form-control chosen' multiple='multiple'");?>
                            </div>
                        </div>
                        <div class='col-md-3 col-sm-6'><?php echo html::submitButton($lang->measure->query, '', 'btn btn-primary');?></div>
                    </div>
                </form>
            </div>
        </div>
        <?php if(empty($members)):?>
            <div class="cell">
                <div class="table-empty-tip">
                    <p><span class="text-muted"><?php echo $lang->error->noData;?></span></p>
                </div>
            </div>
        <?php else:?>
            <div class='cell'>
                <div class='panel'>
                    <div class="panel-heading">
                        <div class="panel-title">
                            <div class="table-row" id='conditions'>
                                <div class="col-xs"><?php echo $title;?></div>
                            </div>
                        </div>
                        <nav class="panel-actions btn-toolbar">
                            <?php if(common::hasPriv('measure', 'exportparticworkdetail')) echo html::a(inLink('exportparticworkdetail', array('param' => $param)), $lang->export, '', 'class="iframe btn btn-primary btn-sm"');?>
                        </nav>
                    </div>
                    <div data-ride='table'>
                        <table class='table table-condensed table-striped table-bordered table-fixed no-margin' id='programList'>
                            <thead>
                            <tr class=''>
                                <th class='w-20px text-left'><?php echo $lang->measure->number;?></th>
                                <th class='w-30px text-left'><?php echo $lang->measure->spaceID;?></th>
                                <th class='w-90px text-left'><?php echo $lang->kanbanspace->name;?></th>
                                <th class='w-30px text-left'><?php echo $lang->measure->kanbanID;?></th>
                                <th class='w-100px text-left'><?php echo $lang->kanban->name;?></th>
                                <th class='w-30px text-left'><?php echo $lang->measure->cardID;?></th>
                                <th class='w-100px text-left'><?php echo $lang->kanbancard->name;?></th>
                                <th class='w-80px text-left'><?php echo $lang->project->blockDeptName;?></th>
                                <th class='w-60px text-left'><?php echo $lang->project->blockMember;?></th>
                                <th class="w-80px text-lef"><?php echo $lang->measure->workhours;?></th>
                                <th class="w-80px text-lef"><?php echo $lang->measure->workLogs;?></th>
                                <th class="w-60px text-lef"><?php echo $lang->measure->workDate;?></th>
                                <th class="w-80px text-lef"><?php echo $lang->measure->createdDate;?></th>
                            </tr>
                            </thead>
                            <tbody id="tbody">
                            <?php $i = 0;?>
                            <?php foreach ($members as $member): $i++?>
                                <tr class="text-center">
                                    <td class='text-left'><?php echo $i;?></td>
                                    <td class='text-left'><?php echo $member->project;?></td>
                                    <td class='text-left' title="<?php echo $member->spaceName?>"><?php echo $member->spaceName?></td>
                                    <td class='text-left'><?php echo $member->execution?></td>
                                    <td class="text-left" title="<?php echo $member->kanbanName;?>"><?php echo $member->kanbanName;?></td>
                                    <td class='text-left'><?php echo $member->objectID?></td>
                                    <td class="text-left" title="<?php echo $member->cardName;?>"><?php echo $member->cardName;?></td>
                                    <td class="text-left" title="<?php echo $member->deptName;?>"><?php echo $member->deptName;?></td>
                                    <td class="text-left"><?php echo $member->realName;?></td>
                                    <td class="text-left"><?php echo $member->workload;?></td>
                                    <td class="text-left" title="<?php echo $member->work;?>"><?php echo $member->work;?></td>
                                    <td class="text-left"><?php echo $member->date;?></td>
                                    <td class="text-left"><?php echo $member->realDate;?></td>
                                </tr>
                            <?php endforeach;?>
                            <tr class="text-center">
                                <td class='text-left'>合计</td>
                                <td class='text-left'><?php echo $amount['count'];?></td>
                                <td class='text-left'><?php echo $amount['count'];?></td>
                                <td class='text-left'><?php echo $amount['kanbans'];?></td>
                                <td class='text-left'><?php echo $amount['kanbans'];?></td>
                                <td class='text-left'><?php echo $amount['cards'];?></td>
                                <td class='text-left'><?php echo $amount['cards'];?></td>
                                <td class='text-left'><?php echo $amount['depts'];?></td>
                                <td class='text-left'><?php echo $amount['user'];?></td>
                                <td class="text-left"><?php echo $amount['total'];?></td>
                                <td class="text-left">-</td>
                                <td class="text-left">-</td>
                                <td class="text-left">-</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif;?>

    </div>
</div>

<?php include '../../common/view/footer.html.php';?>
<script>
    function changeSpace(val) {
        var spaceId = "";
        $("#spaceId option:selected").each(function () {
            spaceId += $(this).val()+',';
        })
        var url = '<?php echo $this->createLink('measure', 'ajaxGetkanban')?>'
        $.post(url,{spaceId:spaceId},function (res) {
            $('#kanbanId').next().remove();
            $('#kanbanId').replaceWith(res);
            $('#kanbanId').chosen();
        })
    }
</script>
