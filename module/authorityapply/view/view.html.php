<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<?php js::import($jsRoot . 'bpmn/bpmn-js-6.1.2.js'); ?>
<?php js::import($jsRoot . 'xm-select.js') ?>
<?php js::set('svnAuthority', $svnAuthority) ?>
<?php js::set('gitlabAuthority', $gitlabAuthority) ?>
<?php js::set('jenkinsAuthority', $jenkinsAuthority) ?>

<?php js::set('ztPermission', $ztPermission) ?>
<?php js::set('svnPermission', $svnPermission) ?>
<?php js::set('gitLabPermission', $gitLabPermission) ?>
<?php js::set('jenkinsPermission', $jenkinsPermission) ?>

<?php js::set('realPermission', ""); ?>
<?php js::set('content', ""); ?>


<style>
    textarea {
        resize: none;
    }

    .chosen-disabled {
        cursor: default;
        opacity: 1 !important;
    }

    .chosen-disabled .chosen-choices .search-choice .search-choice-close {
        cursor: not-allowed !important;
    }

    .tab {
        width: 120px;
        height: 30px;
        line-height: 30px;
        text-align: center;
        border: 1px solid #cccccc;
        margin-right: 2px;
        background-color: #F5F5F5;
        cursor: pointer;
        border-radius: 3px;
    }

    .tab:hover {
        background-color: #7EC5FF;
        color: #fff
    }

    .active1 {
        background-color: #ffffff;
    }

    .nodeSuccess .djs-visual > :nth-child(1) {
        stroke: #52c41a !important;
        stroke-width: 3px;
    }

    .nodeProcessing .djs-visual > :nth-child(1) {
        fill: #1b85ff !important;
        stroke-width: 3px;
    }

    .nodeProcessing .djs-visual > :nth-child(2) {
        fill: #f6fff7 !important;
    }

    #canvas > div > a {
        visibility: hidden !important;
    }
    .search-choice-close{
        display:none !important;
    }
    .xm-label-block span{
        width:auto !important;
    }
    .xm-icon-close{
        display:none !important;
    }
</style>

<div id="mainContent" class="main-content fade">

    <div class="center-block">
        <div class="flex-container" style="margin-top:8px;">
            <div class="notice">
                <?php echo $lang->authorityapply->notice; ?>
            </div>
            <div>
                <?php foreach ($noticeList?$noticeList:$lang->authorityapply->noticeList as $k => $v): ?>
                    <div class="notice-content">
                        <?php echo $v; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="flex-container" style="margin-top: 20px;margin-bottom: 20px">
            <div class="tab active1" style="margin-left: 20px" id="applyInfoBtn" onclick="applyInfoBtnClick()">
                申请信息
            </div>
            <div class="tab" id="historyRecordBtn" onclick="historyRecordBtnClick()">
                历史记录
            </div>
            <div class="tab" id="flowImgBtn" onclick="flowImgBtnClick()">
                流程图
            </div>
        </div>

        <div id="applyInfo">
            <form class="load-indicator main-form form-ajax" method="post" enctype="multipart/form-data" id="dataform">
                <table class="table table-form" id="">
                    <tbody>
                    <tr>
                        <th><?php echo $lang->authorityapply->code; ?></th>
                        <td colspan='5'><?php echo html::input('code', $info->code, "class='form-control'  readonly"); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->authorityapply->summary; ?></th>
                        <td colspan='5'><?php echo html::input('summary', $info->summary, "class='form-control' readonly "); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->authorityapply->createdBy; ?></th>
                        <td colspan='2'><?php echo html::input('createdBy',   zget($userList, $info->createdBy, ''), "class='form-control' readonly"); ?></td>
                        <th><?php echo $lang->authorityapply->applyDepartment; ?></th>
                        <td colspan='2'> <?php echo html::select('applyDepartment', $deptList, $info->applyDepartment, "class='form-control chosen'    "); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->authorityapply->project; ?></th>
                        <td colspan='5'>
                            <?php echo html::select('project[]', $projectList, $info->project, 'class="form-control chosen" multiple '); ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->authorityapply->application; ?></th>
                        <td colspan='5'> <?php echo html::select('application[]', $appList, $info->application, 'class="form-control chosen" multiple  '); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->authorityapply->product; ?></th>
                        <td colspan='5'> <?php echo html::select('product[]', $productList, $info->product, 'class="form-control chosen" multiple '); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->authorityapply->content; ?></th>
                        <td colspan='5'>

                            <table class="table table-bordered ">
                                <tbody id="p-content">
                                <tr>
                                    <th style="text-align: center;"><?php echo $lang->authorityapply->involveSubSystem; ?>
                                    </th>
                                    <th style="text-align: center;"><?php echo $lang->authorityapply->content; ?>
                                    </th>
                                    <th style="text-align: center;"><?php echo $lang->authorityapply->openPermissionPerson; ?>
                                </tr>

                                <?php if ($info->content) : ?>

                                    <?php
                                    $content = json_decode($info->content, true);
                                    if ($content) {
                                        js::set('content', $content);
                                    }
                                    ?>

                                    <?php $content = json_decode($info->content, true); ?>
                                    <?php foreach ($content as $k => $v): ?>
                                        <tr>
                                            <td class="w-130px">
                                                <?php echo html::select("subSystem[$k]", $lang->authorityapply->subSystemList, $content[$k]["subSystem"], 'class="form-control chosen" data-id="0" onChange=subSystemChange(this)'); ?>
                                            </td>
                                            <td>
                                        <textarea disabled rows="5" name="permissionContent[<?php echo $k; ?>]"
                                                  id="permissionContent<?php echo $k; ?>" data-id="<?php echo $k; ?>"
                                                  class="textarea-inherit"><?php echo $v['permissionContent']; ?></textarea>
                                                <div style="display: flex;align-items: center;justify-content: space-around" id="subContent">
                                                    <div class="xm" id="svnPermissionContent<?php echo $k; ?>"></div>
                                                    <div class="xm" id="gitLabPermissionContent<?php echo $k; ?>"></div>
                                                    <div class="xm" id="jenkinsPermissionContent<?php echo $k; ?>"></div>
                                                    <div class="xm xm1" id="svnPermission<?php echo $k; ?>"></div>
                                                    <div class="xm xm1" id="gitLabPermission<?php echo $k; ?>"></div>
                                                    <div class="xm xm1" id="jenkinsPermission<?php echo $k; ?>"></div>
                                                </div>
                                            </td>
                                            <td class="w-300px">
                                                <?php echo html::select("openPermissionPerson[$k][]", $users, isset($content[$k]["openPermissionPerson"]) ? implode(',', $content[$k]['openPermissionPerson']) : "", "class='form-control chosen' multiple"); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->authorityapply->reason; ?></th>
                        <td colspan='5'>
                            <div style="border: 1px solid #cbd0db;"> <?php echo $info->reason; ?></div>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->authorityapply->approvalDepartment; ?></th>
                        <td colspan='5'>
                            <?php echo html::select('approvalDepartment[]', $deptList, $info->approvalDepartment, 'class="form-control chosen" multiple  onchange="approvalDepartmentChange(this);"'); ?>
                        </td>
                    </tr>
                    <?php if ($info->status == 'ended'): ?>
                        <tr>
                            <th><?php echo $lang->authorityapply->realPermissionContent; ?></th>
                            <td colspan='5'>
                                <table class="table table-bordered ">
                                    <tbody id="p-realContent">
                                    <tr>
                                        <th style="text-align: center;"><?php echo $lang->authorityapply->involveSubSystem; ?>
                                        </th>
                                        <th style="text-align: center;"><?php echo $lang->authorityapply->realOpenPermissionPerson; ?>
                                        </th>
                                        <th style="text-align: center;"><?php echo $lang->authorityapply->realPermissionContent; ?>
                                        </th>
                                    </tr>
                                    <?php
                                    if ($info->realPermission) {
                                        $realPermission = json_decode($info->realPermission, true);
                                    }
                                    ?>
                                    <?php if ($info->realPermission) : ?>
                                        <?php js::set('realPermission', $realPermission); ?>
                                        <?php foreach ($realPermission as $k => $v): ?>
                                            <tr>
                                                <td class="w-200px">
                                                    <?php echo html::select("involveSubSystem[$k]", $lang->authorityapply->subSystemList, $realPermission[$k]["involveSubSystem"], "class='form-control chosen' data-id='$k' id='involveSubSystem$k' onChange='subInvolveSystemChange(this)'"); ?>
                                                </td>
                                                <td class="w-300px"><?php echo html::select("realOpenPermissionPerson[$k][]", $userList, isset($realPermission[$k]["realOpenPermissionPerson"]) && !empty($realPermission[$k]["realOpenPermissionPerson"]) ? implode(',', $realPermission[$k]['realOpenPermissionPerson']) : "", "class='form-control chosen' multiple id='realOpenPermissionPerson$k'"); ?></td>
                                                <td>
                                                    <div style="display: flex;align-items: center;justify-content: space-around">
                                                        <div class="flex-container" id="realZt<?php echo $k; ?>">
                                                            <div class="xm"
                                                                 id="realZtPermissionOperate<?php echo $k; ?>"></div>
                                                        </div>
                                                        <div class="flex-container" id="realSvn<?php echo $k; ?>">
                                                            <div class="xm"
                                                                 id="realSvnPermissionPath<?php echo $k; ?>"></div>
                                                            <div class="xm xm1 ml-10"
                                                                 id="realSvnPermissionOperate<?php echo $k; ?>"></div>
                                                        </div>
                                                        <div class="flex-container" id="realGitLab<?php echo $k; ?>">
                                                            <div class="xm"
                                                                 id="realGitLabPermissionPath<?php echo $k; ?>"></div>
                                                            <div class="xm  xm1 ml-10"
                                                                 id="realGitLabPermissionOperate<?php echo $k; ?>"></div>
                                                        </div>
                                                        <div class="flex-container" id="realJenkins<?php echo $k; ?>">
                                                            <div class="xm"
                                                                 id="realJenkinsPermissionPath<?php echo $k; ?>"></div>
                                                            <div class="xm xm1 ml-10"
                                                                 id="realJenkinsPermissionOperate<?php echo $k; ?>"></div>
                                                        </div>
                                                        <div class="flex-container" id="realOther<?php echo $k; ?>">
                                                            <?php echo html::input("realOtherPermissionOperate[$k]", isset($realPermission[$k]["realOtherPermissionOperate"]) && !empty($realPermission[$k]["realOtherPermissionOperate"]) ? $realPermission[$k]["realOtherPermissionOperate"] : '', "class='form-control' style='width:700px;' id='realOtherPermissionOperate$k'"); ?>
                                                        </div>
                                                    </div>

                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php if (!empty($nodes)): ?>
                    <tr>
                        <th><?php echo $lang->authorityapply->dealOpinion; ?></th>
                        <td colspan='5' class="article-content">

                            <div class="detail-content article-content">
                                <div class="detail-title pull-right">
                                    <?php
                                    if (common::hasPriv('authorityapply', 'showHistoryNodes')) echo html::a($this->createLink('authorityapply', 'showHistoryNodes', 'id=' . $info->id, '', true), $lang->authorityapply->showHistoryNodes, '', "data-toggle='modal' data-type='iframe' data-width='70%' style='color: #0c60e1;'");
                                    ?>
                                </div>
                                <table class="table ops">
                                    <tr>
                                        <th class="w-180px"><?php echo $lang->authorityapply->dealNode; ?></th>
                                        <th class="w-180px"><?php echo $lang->authorityapply->dealer; ?></th>
                                        <th class="w-180px"><?php echo $lang->authorityapply->dealResult; ?></th>
                                        <th class="review-opinion"><?php echo $lang->authorityapply->dealOpinion; ?></th>
                                        <th class="w-180px"><?php echo $lang->authorityapply->dealTime; ?></th>
                                    </tr>
                                    <?php
                                    $a = 0;
                                    foreach ($nodes as $key => $reviewNode) {
                                        if (in_array($reviewNode['nodeName'], $lang->authorityapply->deptApprovalStatus) && !empty($reviewNode['toDealUser'][0]) &&!($reviewNode['nodeName']=='returned'&&$reviewNode['result']=='reject')) {
                                            $a = $a + 1;
                                        }
                                    }

                                    foreach ($nodes as $key => $reviewNode):
                                        if (!isset($lang->authorityapply->statusArray[$reviewNode['nodeName']])) {
                                            continue;
                                        }
                                        $nodeCode = $reviewNode['nodeName'];
                                        $nodeName = zget($lang->authorityapply->statusList, $reviewNode['nodeName']);
                                        $nodeDealUsers = implode(',', $reviewNode['toDealUser']);
                                        $reviewerUsers = zmget($users, $nodeDealUsers);
                                        ?>

                                        <?php if ($reviewNode['result'] && $reviewNode['nodeName']!='waitsubmit' && $reviewNode['result'] != 'ignore'): ?>
                                        <tr>
                                            <?php if (in_array($reviewNode['nodeName'], $lang->authorityapply->deptApprovalStatus)): ?>
                                                <?php if ($reviewNode['nodeName'] == 'waitapplyassigned'): ?>
                                                    <th <?php echo "rowspan='$a'"; ?> >
                                                        <?php echo '部门负责人审批' ?>
                                                    </th>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <th><?php echo $nodeName; ?></th>
                                            <?php endif; ?>

                                            <td title="<?php echo $reviewerUsers; ?>"><?php echo $reviewerUsers; ?> </td>
                                            <td>
                                                <?php if ($reviewNode['result'] == 'return'): ?>
                                                    <?php echo '已撤回' ?>
                                                <?php elseif ($reviewNode['result'] == 'terminate'): ?>
                                                    <?php echo '已终止' ?>

                                                <?php else: ?>
                                                    <?php echo zget($lang->authorityapply->reviewList, $reviewNode['result']); ?>

                                                    <?php if ($reviewNode['dealUser']): ?>
                                                        （<?php echo zget($users, $reviewNode['dealUser']); ?>）
                                                    <?php endif; ?>

                                                <?php endif; ?>

                                            </td>
                                            <td style="white-space: pre-line"> <?php echo $reviewNode['comment']; ?></td>
                                            <td> <?php echo $reviewNode['dealDate']; ?></td>
                                        </tr>
                                    <?php endif; ?>
                                    <?php endforeach; ?>
                                </table>
                                <?php endif; ?>
                            </div>

                        </td>

                    </tr>


                    </tbody>
                </table>
            </form>
        </div>

        <div id="historyRecord" style="display:none">

            <div class="cell"><?php include '../../common/view/action.html.php'; ?></div>
        </div>
        <div id="flowImg" style="display:none">
            <div class="detail">
                <div class="detail-title">
                    当前状态：<?php echo in_array($info->status, $lang->authorityapply->approvalStatus) ? $lang->authorityapply->searchStatusList['waitapproval'] : zget($lang->authorityapply->searchStatusList, $info->status); ?>
                </div>
                <div class="flex-container" style="justify-content: space-around;width: 400px;margin-top:20px;">
                    <div style="width:100px;height:32px;text-align: center;line-height: 32px;background-color: #58c6f6;border-radius: 2px;color:#ffffff">
                        已完成
                    </div>
                    <div style="width:100px;height:32px;text-align: center;line-height: 32px;background-color: #facd91;border-radius: 2px;color:#ffffff">
                        进行中
                    </div>
                    <div style="width:100px;height:32px;text-align: center;line-height: 32px;border:1px solid;border-radius: 2px;">
                        未开始
                    </div>
                </div>

                <div class="detail-content article-content">
                    <div id="canvas">

                    </div>
                </div>
            </div>
        </div>
        <div style="text-align: center; width:1200px;">
            <div class="">
                <?php  common::printBack($returnUrl, 'btn btn-wide btn-primary'); ?>
            </div>
        </div>

    </div>
</div>

<?php
js::set('status', $info->status);
js::set('bpmnXML', $flowImg);
js::set('highLightedActivityIdList', isset($reviewFlowInfo->highLightedActivityIdList) ? $reviewFlowInfo->highLightedActivityIdList : []);
js::set('highLightedFlowsIds', isset($reviewFlowInfo->highLightedFlowsIds) ? $reviewFlowInfo->highLightedFlowsIds : []);
js::set('runningActivityIdList', isset($reviewFlowInfo->runningActivityIdList) ? $reviewFlowInfo->runningActivityIdList : []);
?> <!--必须XMl格式-->
<script>
    var ul = document.getElementById('flowImgBtn');
    ul.addEventListener('click', function (e) {
        $('#canvas').empty();
        if (bpmnXML) {
            var bpmnJS = new BpmnJS({
                container: '#canvas',
                width: 1300,
                height: 650,
            });

            // import diagram
            bpmnJS.importXML(bpmnXML, function (err) {
                if (!err) {
                    //设置颜色的方法
                    // const propertiesConfig = [
                    //     {
                    //         nodeName: "waitsubmit",
                    //         fill: "#12c2e9",
                    //     },
                    //     {
                    //         nodeName: "waitapplyassigned",
                    //         fill: "#ffc2e9",
                    //     },
                    //     {
                    //         nodeName: "waitpermissionassigned",
                    //         fill: "#12ca59",
                    //     },
                    //     {
                    //         nodeName: "waitleaderassigned",
                    //         fill: "#1d62e9",
                    //     },
                    //     {
                    //         nodeName: "waitcmassigned",
                    //         fill: "#1fa2e9",
                    //     },
                    //     {
                    //         nodeName: "returned",
                    //         fill: "#5ea2e9",
                    //     },
                    //     {
                    //         nodeName: "withdrawn",
                    //         fill: "#12aaa9",
                    //     },
                    //
                    // ];

                    const setNodeColor = function (ids, newBpmn, colorClass) {
                        const elementRegistry = newBpmn.get('elementRegistry');

                        if (elementRegistry._elements[ids]) {
                            const element = elementRegistry._elements[ids].gfx
                            var a = $(element).children().get(0)
                            var b = '';
                            if (ids.includes('Gateway')) {
                                b = a.querySelector('polygon')
                            } else if (ids.includes('Flow')) {
                                c = a.querySelector('g path')
                                c.style.setProperty('stroke', colorClass)
                                return

                            } else {
                                b = a.querySelector('rect')
                                var textColor = a.querySelector('text')
                                textColor.style.setProperty('fill', "#ffffff")
                            }
                            b.style.setProperty('fill', colorClass)
                            b.style.setProperty('stroke', colorClass)
                        }
                    };
                    var canvas = bpmnJS.get('canvas');
                    canvas.zoom('fit-viewport');

                    if (status == "terminated" || status == "ended") { //结束标记
                        runningActivityIdList = ['end'];
                    } else if (status == '') {
                        runningActivityIdList = [];
                    }
                    if (highLightedActivityIdList.length > 0) {
                        highLightedActivityIdList.map((item) => {
                            // 已经走过的节点中网关颜色
                            if (item.includes('Gateway')) {
                                // setNodeColor(item, bpmnJS, "#dd4e32");
                            }
                            // 已经走过的节点颜色
                            else if (item == 'start') {
                                return
                            } else if (item == status) {
                                setNodeColor(item, bpmnJS, "#facd91");

                            } else {
                                setNodeColor(item, bpmnJS, "#58c6f6");
                            }
                            // propertiesConfig.map((item1) => {
                            //     if (item == item1.nodeName) {
                            //         setNodeColor(item1.nodeName, bpmnJS, item1.fill);
                            //     }
                            // })
                        })
                        // 已经走过的节点连线颜色
                        highLightedFlowsIds.map((item) => {
                            setNodeColor(item, bpmnJS, "#22efc9");
                        })

                    } else {
                        return console.error('could not import BPMN 2.0 diagram', err);
                    }
                }


            })
        }
    })

</script>

<?php include '../../common/view/footer.html.php'; ?>

