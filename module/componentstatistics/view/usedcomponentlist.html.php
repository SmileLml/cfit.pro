<?php include '../../common/view/header.html.php';?>
<?php js::import($this->app->getWebRoot() . "js/" . 'datatable/min.js'); ?>
<?php css::import($this->app->getWebRoot() . "js/" . 'datatable/min.css'); ?>
<?php if(common::checkNotCN()):?>
    <style>#conditions .col-xs {width: 126px;}</style>
<?php endif;?>
<style>
    .datatable-head-cell{text-align:center;}
    .datatable.sortable .datatable-head-span .table>thead>tr>th:after{content:"\f0dc";font-family:"ZentaoIcon";}

    .datatable.sortable .datatable-head-span .table>thead>tr>th.sort-up:after{content:"\f0de";}
    .datatable.sortable .datatable-head-span .table>thead>tr>th.sort-down:after{content:"\f0dd";}
    .filterclick{cursor: pointer;}
    .filterclick .input-group-addon{color: inherit;}
    #datatable-tableaccount{margin-bottom:0px;}
</style>
<div id='mainContent' class='main-row'>
    <div class='side-col col-lg' id='sidebar'>
        <?php include './blockreportlist.html.php';?>
    </div>
    <div class='main-col'>
        <div class='cell'>
            <div class="with-padding">
                <form method='post'>
                    <div class="table-row" id='conditions'>
                        <div class='w-130px col-md-2 col-sm-2' >
                            <div style="line-height:24px;"><?php echo $lang->componentstatistics->componentStartUseTime;?>: </div>
                        </div>
                        <?php
                        foreach ($lang->componentstatistics->quartersDate as $filterkey=>$filterdate){
                            ?>
                            <div class='w-220px col-md-3 col-sm-6'>
                                <div class='input-group filterclick <?php if($selectfilterkey==$filterkey){ echo 'text-blue';}?>' data-key="<?php echo $filterkey;?>">

                                    <span class='input-group-addon'><?php echo $filterdate;?></a> </span>

                                </div>
                            </div>
                            <?php
                        }
                        ?>
                        <div class='w-220px col-md-3 col-sm-6'>
                            <div class='input-group'>
<!--                                <span class='input-group-addon'>--><?php //echo $lang->componentstatistics->begin;?><!--</span>-->
                                <div class='datepicker-wrapper datepicker-date'><?php echo html::input('startTime', $filterDate['startTime'], "class='form-control riqichajian' ");?></div>
                            </div>
                        </div>
                        <div class='w-220px col-md-3 col-sm-6'>
                            <div class='input-group'>
                                <!--                                <span class='input-group-addon'>--><?php //echo $lang->componentstatistics->begin;?><!--</span>-->
                                <div class='datepicker-wrapper datepicker-date'><?php echo html::select('startQuarter',$lang->componentstatistics->quarters, $filterDate['startQuarter'], "class='form-control chosen' ");?></div>
                            </div>
                        </div>
                        <div class='w-220px col-md-3 col-sm-6'>
                            <div class='input-group'>
<!--                                <span class='input-group-addon'>--><?php //echo $lang->componentstatistics->end;?><!--</span>-->
                                <div class='datepicker-wrapper datepicker-date'><?php echo html::input('endTime', $filterDate['endTime'], "class='form-control riqichajian' ");?></div>
                            </div>
                        </div>
                        <div class='w-220px col-md-3 col-sm-6'>
                            <div class='input-group'>
                                <!--                                <span class='input-group-addon'>--><?php //echo $lang->componentstatistics->end;?><!--</span>-->
                                <div class='datepicker-wrapper datepicker-date'><?php echo html::select('endQuarter', $lang->componentstatistics->quarters,$filterDate['endQuarter'], "class='form-control chosen' ");?></div>
                            </div>
                        </div>

                        <div class='col-md-3 col-sm-6'><?php echo html::submitButton($lang->componentstatistics->seqrchQuery, '', 'btn btn-primary');?></div>
                    </div>
                    <div class="table-row" id='conditions2'>
                        <input type="hidden" id="filterkey" name="filterkey" value="<?php echo $selectfilterkey;?>" />
                    </div>
                </form>
            </div>
        </div>
        <?php if(empty($data)):?>
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
                            <?php if(common::hasPriv('componentstatistics', 'exportUsedComponentList')) echo html::a(inLink('exportUsedComponentList'), $lang->export, '', 'class="iframe btn btn-primary btn-sm"');?>
                        </nav>
                    </div>
                    <div data-ride='table'>
                        <table class='table datatables has-sort-head table-bordered' id="tableaccount">
                            <thead>
                            <tr class='text-center'>
                                <th  class=''><a><?php echo $lang->componentstatistics->componentName;?></a></th>
                                <?php
                                foreach ($numlist as $quarter){
                                    $quarterArr = explode('-',$quarter);
                                    $quarterStr = $quarterArr[0].'年第'.$quarterArr[1].'季度';
                                    ?>
                                    <th data-type="number" class=""><?php echo $quarterStr;?></th>
                                    <?php
                                }
                                ?>
                                <th data-type="number" class=''><?php echo $lang->componentstatistics->heji;?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            foreach($data as $key=>$info):?>
                                <tr class="text-center">

                                    <td ><?php echo $key == '合计' ? $key : $componentNames[$key];?></td>
                                    <?php

                                    $onecount = 0;
                                    foreach($info as $d){
                                        ?>
                                        <td><?php echo $d;?></td>
                                        <?php
                                        $onecount += $d;
                                    }?>
                                    <td><?php echo $onecount;?></td>
                                </tr>
                            <?php endforeach;?>
                            </tbody>
                        </table>
                        <table class='table  datatables has-sort-head table-bordered' id="datatables2">
                            <thead>
                            <tr class='text-center'>

                                <th class='sort-disabled'><?php

                                        echo '';

                                    ?></th>

                                <?php
                                foreach ($numlist as $quarter){

                                    ?>
                                    <th data-type="number" class="sort-disabled"><?php echo '';?></th>
                                    <?php
                                }
                                ?>
                                <th data-type="number" class='sort-disabled'><?php echo '';?></th>

                            </tr>
                            </thead>
                            <tbody>

                            <tr class="text-center">


                                <td><?php echo '合计';?></td>
                                <?php
                                $onecount = 0;
                                foreach($hejiList as $heji){
                                    ?>
                                    <td><?php echo $heji;?></td>

                                    <?php
                                    $onecount += $heji;
                                }?>

                                <td><?php echo $onecount;?></td>
                            </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif;?>
    </div>
</div>

<script>
    $(function(){
        $("#startTime").datetimepicker({
            startView:4,
            minView:4,

            format:'yyyy'
        });
        $("#endTime").datetimepicker({
            startView:4,
            minView:4,

            format:'yyyy'
        });

        var fiterDateList = <?php echo json_encode($fiterDateList);?>;

        $(".filterclick").click(function (){
            let clickfilter = $(this).attr("data-key");
            $(".filterclick").removeClass('text-blue');
            $(this).addClass("text-blue");

            $("#startTime").val(fiterDateList[clickfilter]['startTime']);
            $("#startTime").data('datetimepicker').update();

            /*$("#startQuarter").on('change',function(){

            });*/
            $("#startQuarter").val(fiterDateList[clickfilter]['startQuarter'])
            $("#startQuarter").trigger('chosen:updated');


            $("#endTime").val(fiterDateList[clickfilter]['endTime']);
            $("#endTime").data('datetimepicker').update();
            $("#endQuarter").val(fiterDateList[clickfilter]['endQuarter'])
            $("#endQuarter").trigger('chosen:updated');

            $("#filterkey").val(clickfilter);

        });


        $("table.datatables").datatable({
            sortable: true
        });
        $("#datatable-datatables2 .datatable-head").hide();
    });
</script>
<?php include '../../common/view/footer.html.php';?>
