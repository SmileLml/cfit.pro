<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->productionchange->create;?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <table class="table table-form">
                <tbody>
                    <tr>
                        <!--申请人 -->
                        <th class='w-180px'><?php echo $lang->productionchange->applicant;?></th>
                        <td class="required"><?php echo html::select('applicant', $users, $productionChangeInfo->applicant, "class='form-control chosen' onchange='changeApplicant(this.value)'");?></td>
                        <!--申请人所属部门-->
                        <td>
                            <div class='input-group'>
                                <span class='input-group-addon'><?php echo $lang->productionchange->applicantDept;?></span>
                                <?php echo html::select('applicantDept', $depts, $productionChangeInfo->applicantDept, "class='form-control chosen'");?>
                            </div>
                        </td>
                    </tr>

                    <th><?php echo $lang->productionchange->onlineType;?></th>
                    <!--上线申请类型 -->
                    <td class="required"><?php echo html::select('onlineType', $lang->productionchange->onlineTypeList, $productionChangeInfo->onlineType, "class='form-control chosen'");?></td>
                    <!--应用系统名称 多选-->
                    <td>
                        <div class='input-group'>
                            <span class='input-group-addon'><?php echo $lang->productionchange->application;?></span>
                            <?php echo html::select('application[]', $apps, $productionChangeInfo->application, "class='form-control chosen'multiple");?>
                        </div>
                    </td>

                    <tr>
                        <!--上线计划实施时间 开始时间、结束时间 -->
                        <th><?php echo $lang->productionchange->onlineStart;?><i title="<?php echo $lang->productionchange->timeHelp;?>" class="icon icon-help"></i></th>
                        <td class="required"><?php echo html::input('onlineStart', $productionChangeInfo->onlineStart, "class='form-control form-datetime'");?></td>
                        <td>
                            <div class='input-group'>
                                <span class='input-group-addon'><?php echo $lang->productionchange->onlineEnd;?></span>
                                <?php echo html::input('onlineEnd', $productionChangeInfo->onlineEnd, "class='form-control form-datetime'");?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <!--上线摘要 -->
                        <th><?php echo $lang->productionchange->abstract;?></th>
                        <td colspan='2' class="required"><?php echo html::textarea('abstract', $productionChangeInfo->abstract, "rows='6' class='form-control'"); ?></td>
                    </tr>
                    <tr>
                        <!--上线实施内容 -->
                        <th><?php echo $lang->productionchange->implementContent;?></th>
                        <td colspan='2' class="required"><?php echo html::textarea('implementContent', $productionChangeInfo->implementContent, "class='form-control'"); ?></td>
                    </tr>
                    <tr>
                        <!--上线影响说明 -->
                        <th><?php echo $lang->productionchange->effect;?></th>
                        <td colspan='2' class="required"><?php echo html::textarea('effect', $productionChangeInfo->effect, "class='form-control'"); ?></td>
                    </tr>
                    <tr>
                        <!--是否影响关联系统 -->
                        <th><?php echo $lang->productionchange->ifEffectSystem;?></th>
                        <td><?php echo html::radio('ifEffectSystem', $lang->productionchange->ifEffectSystemList,$productionChangeInfo->ifEffectSystem,"onchange='changeCheck(this.value)'");?></td>
                    </tr>
                    <tr class="effectSystemExplain">
                        <!--影响关联系统说明 -->
                        <th><?php echo $lang->productionchange->effectSystemExplain;?></th>
                        <td colspan='2' class="required"><?php echo html::textarea('effectSystemExplain', $productionChangeInfo->effectSystemExplain, "class='form-control'"); ?></td>
                    </tr>
                    <tr>
                        <!--上线材料说明 -->
                        <th><?php echo $lang->productionchange->materialExplain;?></th>
                        <td colspan='2' class="required"><?php echo html::textarea('materialExplain', $productionChangeInfo->materialExplain, "class='form-control'"); ?></td>
                    </tr>
                    <tr>
                        <!--空间 -->
                        <th><?php echo $lang->productionchange->space;?></th>
                        <td colspan='2'><?php echo html::select('space', $getProjects, $productionChangeInfo->space, "class='form-control chosen' onchange='changeSpace(this.value)'");?></td>
                    </tr>
                    <tr>
                        <!--关联发布 -->
                        <th><?php echo $lang->productionchange->correlationPublish;?></th>
                        <td colspan='2'><?php echo html::select('correlationPublish[]', $releases, $productionChangeInfo->correlationPublish, "class='form-control chosen' multiple");?></td>
                    <tr>
                    <tr>
                        <!--关联需求条目 -->
                        <th><?php echo $lang->productionchange->correlationDemand;?></th>
                        <td colspan='2'><?php echo html::select('correlationDemand[]', $demands, $productionChangeInfo->correlationDemand, "class='form-control chosen' multiple");?></td>
                    </tr>
                    <tr>
                        <!--关联问题单 -->
                        <th><?php echo $lang->productionchange->correlationProblem;?></th>
                        <td colspan='2'><?php echo html::select('correlationProblem[]', $problems, $productionChangeInfo->correlationProblem, "class='form-control chosen' multiple");?></td>
                    </tr>
                    <tr>
                        <!--关联工单 -->
                        <th><?php echo $lang->productionchange->correlationSecondorder;?></th>
                        <td colspan='2'><?php echo html::select('correlationSecondorder[]', $secondorders, $productionChangeInfo->correlationSecondorder, "class='form-control chosen' multiple");?></td>
                    </tr>
                    <!-- 待提交和 -->
                    <?php if(in_array($productionChangeInfo->status,array('wait','feedback'))):?>
                    <tr>
                        <!--是否上报 -->
                        <th><?php echo $lang->productionchange->ifReport;?></th>
                        <td><?php echo html::radio('ifReport', $lang->productionchange->ifReportList, $productionChangeInfo->ifReport,"onchange='changeReport(this.value)'");?></td>
                    </tr>
                    <tr class="deptConfirmPerson">
                        <!--部门确认责任人 多选 -->
                        <th><?php echo $lang->productionchange->deptConfirmPerson;?></th>
                        <td colspan='2' class="required">
                            <?php echo html::select('deptConfirmPerson[]', $users, $productionChangeInfo->deptConfirmPerson, "class='form-control chosen'multiple");?>
                        </td>
                    </tr>
                    <tr>
                        <!--业务方接口人 多选 -->
                        <th><?php echo $lang->productionchange->interfacePerson;?></th>
                        <td colspan='2'>
                            <?php echo html::select('interfacePerson[]', $users, $productionChangeInfo->interfacePerson, "class='form-control chosen'multiple");?>
                        </td>
                    </tr>
                    <tr>
                        <!--运维方接口人 多选 -->
                        <th><?php echo $lang->productionchange->operationPerson;?></th>
                        <td colspan='2'>
                            <?php echo html::select('operationPerson[]', $users, $productionChangeInfo->operationPerson, "class='form-control chosen'multiple");?>
                        </td>
                    </tr>
                    <?php else:?>
                    <tr>
                        <!--是否上报 -->
                        <th><?php echo $lang->productionchange->ifReport;?></th>
                        <td><?php echo html::radio('ifReport', $lang->productionchange->ifReportList, $productionChangeInfo->ifReport,"onchange='changeReport(this.value)' disabled");?></td>
                    </tr>
                        <tr class="deptConfirmPerson">
                            <!--部门确认责任人 多选 -->
                            <th><?php echo $lang->productionchange->deptConfirmPerson;?></th>
                            <td colspan='2' class="required">
                                <?php echo html::select('deptConfirmPerson[]', $users, $productionChangeInfo->deptConfirmPerson, "class='form-control chosen' multiple disabled");?>
                            </td>
                        </tr>
                        <tr>
                            <!--业务方接口人 多选 -->
                            <th><?php echo $lang->productionchange->interfacePerson;?></th>
                            <td colspan='2'>
                                <?php echo html::select('interfacePerson[]', $users, $productionChangeInfo->interfacePerson, "class='form-control chosen' multiple disabled");?>
                            </td>
                        </tr>
                        <tr>
                            <!--运维方接口人 多选 -->
                            <th><?php echo $lang->productionchange->operationPerson;?></th>
                            <td colspan='2'>
                                <?php echo html::select('operationPerson[]', $users, $productionChangeInfo->operationPerson, "class='form-control chosen' multiple disabled");?>
                            </td>
                        </tr>

                    <?php endif;?>
                    <tr>
                        <!--介质包获取地址 -->
                        <th><?php echo $lang->productionchange->mediaPackage;?></th>
                        <td colspan='2'><?php echo html::textarea('mediaPackage', $productionChangeInfo->mediaPackage, "class='form-control'"); ?></td>
                    </tr>
                    <!--附件 -->

                    <tr>
                        <th><?php echo $lang->productionchange->fileList ?></th>
                        <td>
                            <div class='detail'>
                                <div class='detail-content article-content'>
                                    <?php
                                    if ($productionChangeInfo->files) {
                                        echo $this->fetch('file', 'printFiles', array('files' => $productionChangeInfo->files, 'fieldset' => 'false', 'object' => null, 'canOperate' => true, 'isAjaxDel' => true));
                                    } else {
                                        echo "<div class='text-center text-muted'>" . $lang->noData . '</div>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <!--附件 -->
                        <th><?php echo $lang->files;?></th>
                        <td colspan='2'><?php echo $this->fetch('file', 'buildform', 'fileCount=1&percent=0.85');?></td>
                    </tr>
                    <tr>
                        <!--抄送人 多选 -->
                        <th><?php echo $lang->productionchange->mailto;?></th>
                        <td colspan='2'>
                            <?php echo html::select('mailto[]', $users, $productionChangeInfo->mailto, "class='form-control chosen'multiple");?>
                        </td>
                    </tr>
                    <tr>
                        <td class='form-actions text-center' colspan='3'><?php echo html::submitButton($this->lang->save) . html::backButton();?></td>
                    </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>
<?php include '../../common/view/footer.html.php';?>
<script>
    $(document).ready(function () {
        changeCheck(<?php echo $productionChangeInfo->ifEffectSystem;?>);
        changeReport(<?php echo $productionChangeInfo->ifReport;?>);
        // $('.deptConfirmPerson').hide();
    });
    function changeApplicant($val)
    {
        $.get(createLink('productionchange', 'ajaxGetDeptByAccount', "account=" + $val), function (applicantDept) {
            $('#applicantDept_chosen').remove();
            $('#applicantDept').replaceWith(applicantDept);
            $('#applicantDept').chosen();
        });
    }

    function changeCheck($val)
    {
        if($val == '1')
        {
            $('.effectSystemExplain').show();
        }else{
            $('.effectSystemExplain').hide();
        }
    }

    function changeReport($val)
    {
        if($val == '1')
        {
            $('.deptConfirmPerson').show();
            $deptId = $('#applicantDept').val();
            $.get(createLink('productionchange', 'ajaxGetDeptByID', "deptId=" + $deptId), function (deptInfo) {
                $('#deptConfirmPerson_chosen').remove();
                $('#deptConfirmPerson').replaceWith(deptInfo);
                $('#deptConfirmPerson').chosen();
            });

        }else{
            $('.deptConfirmPerson').hide();
        }
    }

    function changeSpace($val)
    {
        $.get(createLink('productionchange', 'ajaxGetRelease', "id=" + $val), function (correlationPublish) {
            $('#correlationPublish_chosen').remove();
            $('#correlationPublish').replaceWith(correlationPublish);
            $('#correlationPublish').chosen();
        });
    }

</script>
