<?php include '../../common/view/header.html.php';?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php
        echo html::a($this->createLink('outsideplan', 'chart', ""), '<span class="text">统计信息</span>', '', "class='btn btn-link active'");
    ?>
      <div class="btn-group"><a href="javascript:;" data-toggle="dropdown" class="btn btn-link" style="border-radius: 4px;"><?php if($getYear) echo $getYear; else echo "全部年份"; ?><span class="caret"></span></a>
          <ul class="dropdown-menu">
              <li><a href="outsideplan-chart.html" class="btn btn-link "><span class="text">全部年份</span></a></li>
              <?php for ($year = 2019; $year <= date('Y')+1; $year ++){?>
              <li><a href="outsideplan-chart-<?php echo $year; ?>.html" class="btn btn-link "><span class="text"><?php echo $year; ?></span></a></li>
              <?php }?>
          </ul></div>
  </div>
    <div class="btn-group"><a href="outsideplan-outlook.html" class="btn btn-link "><span class="text">一览表</span></a>
        <!--      <div class="btn-group"><a href="javascript:;" data-toggle="dropdown" class="btn btn-link" style="border-radius: 4px;">外部计划一览表<span class="caret"></span></a>-->
        <!--          <ul class="dropdown-menu">-->
        <!--              <li><a href="outsideplan-outlook.html" class="btn btn-link "><span class="text">外部计划一览表</span></a></li>-->
        <!--              <li><a href="outsideplan-inlook.html" class="btn btn-link "><span class="text">内部计划一览表</span></a></li>-->
        <!---->
        <!--          </ul></div>-->
    <?php if(common::hasPriv('outsideplan', 'exportChart')) { ?>
    <div class="btn-toolbar pull-right">
        <div class='btn-group'>
            <button class="btn btn-link" data-toggle="dropdown"><i class="icon icon-export muted"></i> <span class="text"><?php echo $lang->export ?></span> <span class="caret"></span></button>
            <ul class="dropdown-menu" id='exportActionMenu'>
                <?php
                $class = common::hasPriv('outsideplan', 'exportChart') ? '' : "class=disabled";
                $misc  = common::hasPriv('outsideplan', 'exportChart') ? "data-toggle='modal' data-type='iframe' class='export'" : "class=disabled";
                $link  = common::hasPriv('outsideplan', 'exportChart') ? $this->createLink('outsideplan', 'exportChart', "") : '#';
                echo "<li $class>" . html::a($link."?type=1", "导出年度数据", '', $misc) . "</li>";
                echo "<li $class>" . html::a($link."?type=2", "导出业务司局数据", '', $misc) . "</li>";
                echo "<li $class>" . html::a($link."?type=3", "导出承建单位数据", '', $misc) . "</li>";
                ?>
            </ul>
        </div>
    </div>
    <?php } ?>
</div>

<div id='mainContent' class='main-row' style="background-color: #FFFFFF">
  <div class='main-col' >
    <div class="cell<?php if($browseType == 'bysearch') echo ' show';?>" id="queryBox" data-module='outsideplan'></div>
        <h4 class="centerTxt">项目计划基本信息统计-按项目状态统计</h4>
        <table class="table table-datatable table-bordered table-condensed table-striped table-fixed table-hover ">
            <tr>
                <th class='w-55px centerTxt' rowspan="3" >年份</th>
                <th class='centerTxt'  colspan="8">(外部)项目/任务计划信息</th>
                <th class='centerTxt'  colspan="13">内部项目计划信息</th>
            </tr>
            <tr>

                <th class='w-55px centerTxt' rowspan="2">(外部)项目/任务<br>总数</th>
                <th class='w-100px centerTxt' rowspan="2">(外部)项目/任务<br>子项总数</th>
                <th class='w-100px centerTxt' rowspan="2">(外部)子项/子任务<br>总数</th>
                <th class='centerTxt' colspan="5">(外部)项目/任务计划状态</th>
                <th class='w-100px centerTxt' rowspan="2">内部项目<br>总数</th>
                <th class='w-100px centerTxt' colspan="11">内部项目状态</th>
                <th class='w-100px centerTxt' rowspan="2">内部项目总<br>数（未关联<br>(外部)项目/任务）</th>
            </tr>
            <tr>
                <th>已创建</th>
                <th>异常完成</th>
                <th>正常完成</th>
                <th>未完成</th>
                <th>进度异常</th>
<!--                内部状态-->
                <th>已取消</th>
                <th>待立项</th>
                <th>延迟立项</th>
                <th>暂停立项</th>
                <th>立项中</th>
                <th>已立项</th>
                <th>进度正常</th>
                <th>进度延迟</th>
                <th>已暂停</th>
                <th>已撤销</th>
                <th>已结项</th>
            </tr>
            <?php foreach ($list['yearList'] as $item) { ?>
            <tr>
                <td><?php echo $item['year']; if($item['year'] == 2019) { echo "及以前";}?></td>
                <td><?php echo $item['idNum']?></td>
                <td><?php echo $item['subs']?></td>
                <td><?php echo $item['tasks']?></td>
                <td><?php echo $item['status']['wait']?></td>
                <td><?php echo $item['status']['exceptionallyfinished']?></td>
                <td><?php echo $item['status']['finished']?></td>
                <td><?php echo $item['status']['notfinished']?></td>
                <td><?php echo $item['status']['exceptionallyprogressing']?></td>
                <td><?php echo $item['projectPlanNum']?></td>
                <td><?php echo $item['insideStatus']['cancel']?></td>
                <td><?php echo $item['insideStatus']['wait']?></td>
                <td><?php echo $item['insideStatus']['projectdelay']?></td>
                <td><?php echo $item['insideStatus']['projectpause']?></td>
                <td><?php echo $item['insideStatus']['projecting']?></td>
                <td><?php echo $item['insideStatus']['projectsetup']?></td>
                <td><?php echo $item['insideStatus']['progressnormal']?></td>
                <td><?php echo $item['insideStatus']['progressdelay']?></td>
                <td><?php echo $item['insideStatus']['pause']?></td>
                <td><?php echo $item['insideStatus']['abort']?></td>
                <td><?php echo $item['insideStatus']['done']?></td>
                <td><?php echo $item['insideUnlinks']?></td>
            </tr>
            <?php } ?>
        </table>
      <h4 class="centerTxt">项目计划基本信息统计-按业务司局/承建单位统计</h4>
      <table class="table table-datatable table-bordered table-condensed table-striped table-fixed table-hover ">
          <tr>
              <th class='w-100px centerTxt'  rowspan="3">业务司局</th>
              <th class='centerTxt'  colspan="9">(外部)项目/任务计划信息</th>
              <th class=' centerTxt'  colspan="12">内部项目计划信息</th>
          </tr>
          <tr>
              <th class='w-10px centerTxt' rowspan="2">年份</th>
              <th class='w-100px centerTxt' rowspan="2">(外部)项目/任务<br>总数</th>
              <th class='w-100px centerTxt' rowspan="2">(外部)项目/任务<br>子项总数</th>
              <th class='w-100px centerTxt' rowspan="2">(外部)子项/子任务<br>总数</th>
              <th class='centerTxt' colspan="5">(外部)项目/任务计划状态</th>
              <th class='w-100px centerTxt' rowspan="2">内部项目<br>总数</th>
              <th class='w-100px centerTxt' colspan="11">内部项目状态</th>
          </tr>
          <tr>
              <th>已创建</th>
              <th>异常完成</th>
              <th>正常完成</th>
              <th>未完成</th>
              <th>进度异常</th>
              <!--                内部状态-->
              <th>已取消</th>
              <th>待立项</th>
              <th>延迟立项</th>
              <th>暂停立项</th>
              <th>立项中</th>
              <th>已立项</th>
              <th>进度正常</th>
              <th>进度延迟</th>
              <th>已暂停</th>
              <th>已撤销</th>
              <th>已结项</th>
          </tr>
          <?php foreach ($list['subTaskUnitList'] as $key => $info) {
              $i = 0;
              foreach ($info as $year => $item) {
              ?>
              <tr>
                  <?php if($i == 0) { ?><td rowspan="<?php echo count($info)?>"><?php echo zget($lang->outsideplan->subProjectUnitList,$key)?></td> <?php } $i++;?>
                  <td><?php echo $year?></td>
                  <td><?php echo $item['idNum']?></td>
                  <td><?php echo $item['subs']?></td>
                  <td><?php echo $item['tasks']?></td>
                  <td><?php echo $item['status']['wait']?></td>
                  <td><?php echo $item['status']['exceptionallyfinished']?></td>
                  <td><?php echo $item['status']['finished']?></td>
                  <td><?php echo $item['status']['notfinished']?></td>
                  <td><?php echo $item['status']['exceptionallyprogressing']?></td>
                  <td><?php echo $item['projectPlanNum']?></td>
                  <td><?php echo $item['insideStatus']['cancel']?></td>
                  <td><?php echo $item['insideStatus']['wait']?></td>
                  <td><?php echo $item['insideStatus']['projectdelay']?></td>
                  <td><?php echo $item['insideStatus']['projectpause']?></td>
                  <td><?php echo $item['insideStatus']['projecting']?></td>
                  <td><?php echo $item['insideStatus']['projectsetup']?></td>
                  <td><?php echo $item['insideStatus']['progressnormal']?></td>
                  <td><?php echo $item['insideStatus']['progressdelay']?></td>
                  <td><?php echo $item['insideStatus']['pause']?></td>
                  <td><?php echo $item['insideStatus']['abort']?></td>
                  <td><?php echo $item['insideStatus']['done']?></td>
              </tr>
          <?php
              }
          } ?>
      </table>
      <table class="table table-datatable table-bordered table-condensed table-striped table-fixed table-hover ">
          <tr>
              <th class='w-100px centerTxt'  rowspan="3">承建单位</th>
              <th class='centerTxt'  colspan="9">(外部)项目/任务计划信息</th>
              <th class='centerTxt'  colspan="12">内部项目计划信息</th>
          </tr>
          <tr>
              <th class='w-10px centerTxt' rowspan="2">年份</th>
              <th class='w-100px centerTxt' rowspan="2">(外部)项目/任务<br>总数</th>
              <th class='w-100px centerTxt' rowspan="2">(外部)项目/任务<br>子项总数</th>
              <th class='w-100px centerTxt' rowspan="2">(外部)子项/子任务<br>总数</th>
              <th class='centerTxt' colspan="5">(外部)项目/任务计划状态</th>
              <th class='w-100px centerTxt' rowspan="2">内部项目<br>总数</th>
              <th class='w-100px centerTxt' colspan="11">内部项目状态</th>
          </tr>
          <tr>
              <th>已创建</th>
              <th>异常完成</th>
              <th>正常完成</th>
              <th>未完成</th>
              <th>进度异常</th>
              <!--                内部状态-->
              <th>已取消</th>
              <th>待立项</th>
              <th>延迟立项</th>
              <th>暂停立项</th>
              <th>立项中</th>
              <th>已立项</th>
              <th>进度正常</th>
              <th>进度延迟</th>
              <th>已暂停</th>
              <th>已撤销</th>
              <th>已结项</th>
          </tr>
          <?php foreach ($list['subTaskBearDeptList'] as $key => $info) {
              $i = 0;
              foreach ($info as $year => $item) {
              ?>
              <tr>
                  <?php if($i == 0) { ?><td rowspan="<?php echo count($info)?>"><?php echo zget($lang->application->teamList,$key)?></td> <?php } $i++;?>
                  <td><?php echo $year?></td>
                  <td><?php echo $item['idNum']?></td>
                  <td><?php echo $item['subs']?></td>
                  <td><?php echo $item['tasks']?></td>
                  <td><?php echo $item['status']['wait']?></td>
                  <td><?php echo $item['status']['exceptionallyfinished']?></td>
                  <td><?php echo $item['status']['finished']?></td>
                  <td><?php echo $item['status']['notfinished']?></td>
                  <td><?php echo $item['status']['exceptionallyprogressing']?></td>
                  <td><?php echo $item['projectPlanNum']?></td>
                  <td><?php echo $item['insideStatus']['cancel']?></td>
                  <td><?php echo $item['insideStatus']['wait']?></td>
                  <td><?php echo $item['insideStatus']['projectdelay']?></td>
                  <td><?php echo $item['insideStatus']['projectpause']?></td>
                  <td><?php echo $item['insideStatus']['projecting']?></td>
                  <td><?php echo $item['insideStatus']['projectsetup']?></td>
                  <td><?php echo $item['insideStatus']['progressnormal']?></td>
                  <td><?php echo $item['insideStatus']['progressdelay']?></td>
                  <td><?php echo $item['insideStatus']['pause']?></td>
                  <td><?php echo $item['insideStatus']['abort']?></td>
                  <td><?php echo $item['insideStatus']['done']?></td>
              </tr>
          <?php
              }
          } ?>
      </table>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
