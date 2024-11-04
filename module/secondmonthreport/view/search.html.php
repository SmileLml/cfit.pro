<div class='cell'>
    <div class="with-padding">
        <form method='post'>

            <div class='w-80px pull-left' >
                <div style="line-height:32px;"><?php echo $lang->secondmonthreport->statisticalCycle;?>

                </div>
            </div>
            <div class="pull-left" id="searchtypewrap" style="margin-right:5px;">
                <select id="searchtype" class="form-control" name="searchtype">
                    <?php
                    if('problemcompletedplan' == $this->app->methodName){
                        unset($lang->secondmonthreport->searchsearchtypeList['realtime']);
                    }
                    foreach ($lang->secondmonthreport->searchsearchtypeList as $key1=>$value1){
                    ?>
                    <option <?php if($searchtype==$key1){ echo 'selected';}?> value="<?php echo $key1;?>"><?php echo $value1;?></option>
                    <?php
                                }
                                ?>
                </select>
            </div>
            <div class=" pull-left" id="histimetypewrap" style="margin-right:5px;">
                <select class="form-control" name="histimetype" id="histimetype" onchange="getSearchDept(this.value)">
                    <?php
                    if(!in_array($this->app->methodName, $lang->secondmonthreport->quarterFormList)){
                        unset($lang->secondmonthreport->searchhistimetypeList['hisquarter']);
                    }
                    if('problemcompletedplan' == $this->app->methodName){
                        unset($lang->secondmonthreport->searchhistimetypeList['hismonth'],$lang->secondmonthreport->searchhistimetypeList['hisyear']);
                    }
                    foreach ($lang->secondmonthreport->searchhistimetypeList as $key2=>$value2){
                    ?>
                    <option <?php if($histimetype==$key2){ echo 'selected';}?> value="<?php echo $key2;?>"><?php echo $value2;?></option>
                    <?php
                            }

                            ?>
                </select>
            </div>
            <div class=" pull-left" id="hisdatelistwrap" style="margin-right:5px;">
                <select class="form-control" name="hisdatelist" id="hisdatelist">

                </select>
            </div>
            <div class=" pull-left hidden" id="realtimetypewrap" style="margin-right:5px;">
                <select class="form-control" name="realtimetype" id="realtimetype">
                    <?php foreach ($lang->secondmonthreport->searchrealtimetypeList as $key3=>$value3){
                    ?>
                    <option <?php if($realtimetype==$key3){ echo 'selected';}?> value="<?php echo $key3;?>"><?php echo $value3;?></option>
                    <?php
                            }
                            ?>
                </select>
            </div>
            <div class='pull-left hidden' id="realstarttimewrap" style="margin-right:5px;">
                <input type="text" value="" name="realstarttime" class="form-control "  id="realstarttime" readonly="readonly" />
            </div>
            <div class='pull-left hidden' id="realendtimewrap" style="margin-right:5px;">
                <input type="text" value="" name="realendtime" class="form-control" id="realendtime" readonly="readonly" />
            </div>
            <div class='pull-left w-160px' style="margin-right:5px;">
                <div class='input-group'>
                    <span class='input-group-addon'><?php echo $lang->secondmonthreport->deptName; ?></span>
                    <?php echo html::select('deptID', $searchdepts, $deptID, "class='form-control chosen' "); ?>
                </div>
            </div>

            <div class=" pull-left ">
                <?php echo html::hidden('sformtype',$staticType);?>
                <?php echo html::submitButton('搜索', 'onclick = "return confirmsubmit()"', 'btn btn-primary');?>
            </div>
            <?php js::set('deptId', $deptID); ?>
            <script>

                $(function(){
                    var stype = $('#histimetype').val();
                    getSearchDept(stype);
                    $("#searchtype").change(function (event){
                        let svalue = this.value;
                        if(svalue == 'history'){
                            $("#realtimetypewrap,#realstarttimewrap,#realendtimewrap").addClass('hidden');
                            $("#histimetypewrap,#hisdatelistwrap").removeClass('hidden');
                            //获取 年度月度表单值
                            let histimetypeval = $("#histimetype").val();
                            let selectDate = '<?php echo $hisdatelist; ?>';
                            getHistoryMonthTypeData(svalue,histimetypeval,selectDate)

                        }else if(svalue == 'realtime'){
                            $("#realtimetypewrap,#realstarttimewrap,#realendtimewrap").removeClass('hidden');
                            $("#histimetypewrap,#hisdatelistwrap").addClass('hidden');
                            let realtimetypeval = $("#realtimetype").val();
                            let searchrealstarttimeval = '<?php echo $realstarttime; ?>';
                            let searchrealendtimeval = '<?php echo $realendtime; ?>';
                            getRealDateRange(svalue,realtimetypeval,searchrealstarttimeval,searchrealendtimeval);
                        }
                    });

                    $(document).delegate("#histimetype",'change',function(){
                        let histimetypeval = $("#histimetype").val();
                        let svalue = $("#searchtype").val();
                        getHistoryMonthTypeData(svalue,histimetypeval,0);
                    })

                    $(document).delegate("#realtimetype",'change',function(event){
                        let realtimetypeval = $("#realtimetype").val();
                        let svalue = $("#searchtype").val();


                        getRealDateRange(svalue,realtimetypeval);

                    })
                    $("#searchtype").trigger('change');

                    $('#histimetype').change(function (){

                    })
                })
                function confirmsubmit(){
                    let searchtype = $("#searchtype").val();
                    let realtimetypeval = $("#realtimetype").val();
                    if(searchtype == 'realtime'){
                        if(realtimetypeval == 'custom'){
                           let starttime =  new Date($("#realstarttime").val()).getTime();
                           let endtime =  new Date($("#realendtime").val()).getTime();
                           if(endtime < starttime){
                               alert('<?php echo $lang->secondmonthreport->searchstarttimeendtimeNotice;?>');
                               return false;
                           }
                        }
                    }

                    return true;
                }
                function getHistoryMonthTypeData(svalue,histimetypeval,selectDate){

                    $.post(createLink("secondmonthreport",'ajaxgetdatalist'),{'searchtype':svalue,'histimetype':histimetypeval,'sformtype':$("#sformtype").val(),'selectDate':selectDate},function (data){
                        $("#hisdatelist").html(data);
                    });
                }
                function getRealDateRange(svalue,realtimetypeval,searchrealstarttimeval='',searchrealendtimeval=''){
                    $.ajaxSetup(
                        {
                            async:false
                        }
                    );
                    $.post(createLink("secondmonthreport",'ajaxgetdaterange'),{'searchtype':svalue,'realtimetype':realtimetypeval,'sformtype':$("#sformtype").val()},function (data){
                        let realtimetypeval = $("#realtimetype").val();
                        $("#realstarttime").datetimepicker('remove');

                        $("#realstarttime").val(data.startday);
                        if(realtimetypeval == 'custom'){
                            $("#realstarttime").datetimepicker({
                                ignoreReadonly:true,
                                weekStart:1,
                                todayBtn:false,
                                autoclose:1,
                                todayHighlight:1,
                                startView:2,
                                minView:2,
                                forceParse:0,
                                format:"yyyy-mm-dd",
                                initialDate:data.startday,
                                startDate:data.calendarstartday,
                                endDate:data.endday
                            });
                            // $("#realendtime").data('DateTimePicker').setMaxDate(data.endday);
                            $("#realstarttime").datetimepicker('setStartDate',data.calendarstartday);
                            $("#realstarttime").datetimepicker('setEndtDate',data.endday);
                            $("#realstarttime").datetimepicker('setInitialtDate',data.startday);

                        }else{
                            $("#realstarttime").datetimepicker({
                                ignoreReadonly:true,
                                weekStart:1,
                                todayBtn:false,
                                autoclose:1,
                                todayHighlight:1,
                                startView:2,
                                minView:2,
                                forceParse:0,
                                format:"yyyy-mm-dd",
                                initialDate:data.startday,
                                startDate:data.startday,
                                endDate:data.startday
                            });
                        }


                        $("#realendtime").datetimepicker('remove');
                        if(realtimetypeval == 'custom'){
                            $("#realendtime").datetimepicker({
                                ignoreReadonly:true,
                                weekStart:1,
                                todayBtn:false,
                                autoclose:1,
                                todayHighlight:1,
                                startView:2,
                                minView:2,
                                forceParse:0,
                                format:"yyyy-mm-dd",
                                initialDate:data.endday,
                                startDate:data.calendarstartday,
                                endDate:data.endday
                            });
                            // $("#realendtime").data('DateTimePicker').setMaxDate(data.endday);
                            $("#realendtime").datetimepicker('setStartDate',data.calendarstartday);
                            $("#realendtime").datetimepicker('setEndtDate',data.endday);
                            $("#realendtime").datetimepicker('setInitialtDate',data.endday);

                        }else{
                            $("#realendtime").datetimepicker({
                                ignoreReadonly:true,
                                weekStart:1,
                                todayBtn:false,
                                autoclose:1,
                                todayHighlight:1,
                                startView:2,
                                minView:2,
                                forceParse:0,
                                format:"yyyy-mm-dd",
                                initialDate:data.endday,
                                startDate:data.endday,
                                endDate:data.endday
                            });
                        }
                        if(searchrealendtimeval){
                            $("#realendtime").val(searchrealendtimeval);
                        }else{
                            $("#realendtime").val(data.endday);
                        }

                        if(searchrealstarttimeval){
                            $("#realstarttime").val(searchrealstarttimeval);
                        }else{
                            $("#realstarttime").val(data.startday);
                        }

                        $("#realstarttime").datetimepicker('update');
                        $("#realendtime").datetimepicker('update');

                        $.ajaxSetup(
                            {
                                async:false
                            }
                        );
                    },'json');

                }

                function getSearchDept(stype)
                {
                    $.post(createLink("secondmonthreport",'ajaxSearchDeptList'),{'searchtype':stype},function (data){
                        $('#deptID_chosen').remove();
                        $('#deptID').replaceWith(data);
                        $('#deptID').val(deptId);
                        $('#deptID').chosen();
                    });
                }
            </script>
        </form>
        <div class="clearfix"></div>
    </div>
</div>