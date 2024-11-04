<?php include '../../common/view/header.html.php';?>

<?php if(common::checkNotCN()):?>
<style>
#conditions .col-xs {width: 126px;}

</style>
<?php endif;?>
<style>
.form-row{
    margin-bottom: 10px;
}
.picker-select:disabled + .picker .picker-selections{
    background-color: #f5f5f5;
}
.picker-select:disabled + .picker{
    pointer-events: none;
    position: relative;
}

.input-group>.input-group-addon:first-child{
    min-width: 90px;
}

</style>
<div id='mainContent' class='main-row'>
  <div class='side-col col-lg'>
    <div class='panel'>
      <div class='panel-heading'>
        <div class='panel-title'><?php echo $lang->qareport->report->select;?></div>
      </div>
      <div class='cell' style='box-shadow: none;'>
        <?php include './commonheader.html.php'?>
      </div>
    </div>
  </div>
  <div class='main-col'>
    <div class='cell'>
      <div class="with-padding">
        <form action='<?php echo $this->createLink('qareport', 'bugtrend', "queryType=search");?>' method='post' id='bugtrendForm'>
          <input id="chartMode" type='hidden' name='chartMode' value=''/>
          <div class="table-row" id='conditions'>
            <div class='form-row'>
              <div class='col-md-4 col-sm-6 input-group'>
                <span class='input-group-addon text-ellipsis'><?php echo $lang->report->startTime;?></span>
                <?php echo html::input('begin', $begin, "class='form-control form-date'");?>
                <span class='input-group-addon text-ellipsis'><?php echo '~';?></span>
                <?php echo html::input('end', $end, "class='form-control form-date'");?>
              </div>
            </div>
            <div class='form-row'>
              <div class='input-group'>
                <span class='input-group-addon text-ellipsis'><?php echo $lang->report->projectOptions;?></span>
                <?php echo html::select('project[]', $projects, $projectList , "class='form-control picker-select' multiple='multiple' data-drop-width='auto'");?>
              </div>
            </div>
            <div class='form-row'>
              <div class='input-group'>
                <span class='input-group-addon text-ellipsis'><?php echo $lang->report->testtaskOptions;?></span>
                <?php echo html::select('testtask[]', $testtasks, $testtaskList, "class='form-control picker-select' multiple='multiple' data-drop-width='auto' placeholder=''");?>
              </div> 
            </div>
            <div class='form-row' style="text-align: center;">
              <?php echo html::submitButton($lang->crystal->query, '', 'btn btn-primary');?>
              <?php echo html::resetButton($lang->report->resetQuery, '', 'btn btn-primary');?>
            </div>
          </div>
        </form>
      </div>
    </div>
    <?php if(empty($trendData)):?>
    <div class="cell">
      <div class="table-empty-tip">
        <p><span class="text-muted"><?php echo $lang->error->noData;?></span></p>
      </div>
    </div>
    <?php else:?>
    <div class='cell'>
      <div class='panel'>
        <div class="panel-heading">
        </div>
      </div>
      <div class='panel-body'>
        <div class="option-header" style="text-align: right;">
          <div class='btn-group dropup'>
          <?php foreach($lang->report->bugTrendPeriods as $key => $value):?>
            <button type='button' class='btn chart-mode <?php echo $key;?>' data-chart-mode="<?php echo $key;?>"><?php echo $value;?></button>
          <?php endforeach;?>
          </div>
          <?php if(common::hasPriv('report', 'bugtrendexport')):?>
          <div class='btn-group' style="text-align: left;">
            <button type='button' class='btn btn-link dropdown-toggle' data-toggle='dropdown' style="color: #0c64eb">
                <i class="icon icon-export" style="color: #0c64eb"></i> <span class="text"><span class="caret" style="opacity: 1;"></span></button>
            </button>
            <ul class='dropdown-menu' id='exportActionMenu'>
                <li><a href="javascript:;" onclick="onExportToImage()"><?php echo $lang->report->exportToImage ?></a></li>
                <li><a href="javascript:;" onclick="onExportToExcel()"><?php echo $lang->report->exportToExcel ?></a></li>
            </ul>
          </div>
          <?php endif; ?>
        </div>
        <div style="display: flex;align-items: baseline">
          <h3>
            <?php echo $lang->report->bugTrend;?>
          </h3>
          <icon class='icon icon-help' style="color:rgb(188,191,201);font-size: 20px;margin-left:5px;cursor: pointer;" data-toggle='popover' data-trigger='focus hover' data-placement='right' data-tip-class='text-muted popover-sm' data-content="<?php echo $lang->report->bugTrendTitleTips;?>"></icon>
        </div>
        <div id="chart-container" style="width: 100%; height:400px;"></div>
        <div style="display: flex;align-items: baseline">
          <h3>
            <?php echo $lang->report->totalBugTrend;?>
          </h3>
          <icon class='icon icon-help' style="color:rgb(188,191,201);font-size: 20px;margin-left:5px;cursor: pointer" data-toggle='popover' data-trigger='focus hover' data-placement='right' data-tip-class='text-muted popover-sm' data-content="<?php echo $lang->report->totalBugTrendTitleTips;?>"></icon>
        </div>
        <div id="chart-container-total" style="width: 100%; height:400px;"></div>
      </div>
    </div>
    <?php endif;?>
  </div>
</div>
<?php if(!empty($trendData)):?>
<?php js::set('lables', $trendData['lables']);?>
<?php js::set('createPairs', $trendData['createPairs']);?>
<?php js::set('resolvedPairs', $trendData['resolvedPairs']);?>
<?php js::set('activatedPairs', $trendData['activatedPairs']);?>
<?php js::set('closedPairs', $trendData['closedPairs']);?>
<?php js::set('totalCreateParis', $trendData['totalCreateParis']);?>
<?php js::set('totalActivatedParis', $trendData['totalActivatedParis']);?>
<?php js::set('totalToCloseParis', $trendData['totalToCloseParis']);?>
<?php js::set('totalToResolveParis', $trendData['totalToResolveParis']);?>
<?php js::set('testtaskIdList', implode(',', $testtaskList));?>
<?php js::set('emptyDate', $lang->report->emptyDate);?>
<?php js::set('greaterEndDate', $lang->report->greaterEndDate);?>
<?php js::set('chartMode', $chartMode); ?>
<?php js::set('bugTrend', $lang->report->bugTrend); ?>
<?php js::set('totalBugTrend', $lang->report->totalBugTrend); ?>
<script>
    $('.chart-mode.'+chartMode).addClass('btn-primary');
</script>
<?php endif;?>
<?php js::set('endDateOutRange', $lang->report->endDateOutRange);?>
<?php js::set('selectProjectTips', $lang->report->selectProjectTips);?>
<?php js::set('noTesttasks', $lang->report->noTesttasks);?>

<script>
  $('.chart-mode').click(function() {
    var chartMode = $(this).data('chart-mode');
    $('#chartMode').val(chartMode);

    $('#submit').click()
  });

  $('#submit').click(function() {
    var begin = $('#begin').val();
    var end   = $('#end').val();

    if(end && begin > end)
    {
        alert(greaterEndDate);
        return false;
    }

    // 不能大于当前日期
    if(end && end > '<?php echo date('Y-m-d');?>')
    {
        alert(endDateOutRange);
        return false;
    }

    $('#bugTrendForm').submit();
});

$('#reset').click(function()
{
    $("#begin").attr('value', '');
    $("#end").attr('value', '');
    $("#chartMode").attr('value', '');

    $("#project option:selected").removeAttr("selected");
    $('#project').trigger('chosen:updated');

    $("#testtask option:selected").removeAttr("selected");
    $('#testtask').trigger('chosen:updated');
});

$("#project").on('change', function()
{
    var projects   = '';
    var projectVal = $(this).val();
    if(projectVal != null)
    {
        projects = projectVal.join(",");
    }
    else
    {
        $("#testtask").prop('disabled', true);
        $("#testtask").val('');
        $('#testtask').next('').find('.picker-selection').remove();
        return;
    }
    
    var testtasks    = '';
    var testtasksVal = $("#testtask").val()
    if(testtasksVal != null)
    {
        var testtasksStr = testtasksVal.join(",");
        if(testtasksStr != '')
        {
            testtasks = testtasksStr;
        }
    }

    var link = createLink('testtask', 'ajaxGetProjectTestTasks', 'projectID=' + projects + '&testtaskID=' + testtasks + '&isMultiple=1');

    $.post(link, function(data)
    {
        if(!$('#project').val()) return;
        $('#testtask').next().remove();
        $('#testtask').replaceWith(data);
        $('#testtask').picker();
    });
});
</script>
<?php if(!empty($trendData)):?>
<?php js::import($jsRoot . 'chart/echarts.min.js'); ?>
<script>
$('[data-toggle="popover"]').popover();

var dom = document.getElementById('chart-container');
var myChart = echarts.init(dom, null, {
  renderer: 'canvas',
  useDirtyRect: false
});
var app = {};

var option;

option = {
  tooltip: {
    trigger: 'axis'
  },
  legend: {
    data: ['<?php echo $lang->report->create ?>', '<?php echo $lang->report->resolve ?>', '<?php echo $lang->report->activate ?>', '<?php echo $lang->report->close ?>']
  },
  grid: {
    left: '3%',
    right: '4%',
    bottom: '3%',
    containLabel: true
  },
  xAxis: {
    type: 'category',
    boundaryGap: false,
    data: lables,
    axisLabel:{
        formatter:function(value){
            return '{weekRange|' +value + '}';
        },
        rich:{
            weekRange:{
                align:'left',
            }
        }
    }
  },
  yAxis: {
    type: 'value',
  },

  series: [
    {
      name: '<?php echo $lang->report->create ?>',
      type: 'line',
      data: createPairs
    },
    {
      name: '<?php echo $lang->report->resolve ?>',
      type: 'line',
      data: resolvedPairs
    },
    {
      name: '<?php echo $lang->report->activate ?>',
      type: 'line',
      data: activatedPairs,
      lineStyle: {
        color:'#FF8C5A '
      },
      itemStyle: {
        color: '#FF8C5A'
      }
    },
    {
      name: '<?php echo $lang->report->close ?>',
      type: 'line',
      data: closedPairs,
      lineStyle: {
        color:'#4EA3FF'
      },
      itemStyle: {
        color: '#4EA3FF'
      }
    },
  ]
};

if (option && typeof option === 'object') {
  myChart.setOption(option);
}

window.addEventListener('resize', myChart.resize);
</script>
<script>
var domTotal = document.getElementById('chart-container-total');
var myChartTotal = echarts.init(domTotal, null, {
  renderer: 'canvas',
  useDirtyRect: false
});
var app = {};

var option;

option = {
  tooltip: {
    trigger: 'axis'
  },
  legend: {
    data: ['<?php echo $lang->report->totalCreate ?>','<?php echo $lang->report->totalActivate ?>','<?php echo $lang->report->totalToClose ?>','<?php echo $lang->report->totalToResolve ?>']
  },
  grid: {
    left: '3%',
    right: '4%',
    bottom: '3%',
    containLabel: true
  },
  xAxis: {
    type: 'category',
    boundaryGap: false,
    data: lables
  },
  yAxis: {
    type: 'value',
  },

  series: [
    {
        name: '<?php echo $lang->report->totalCreate ?>',
        type: 'line',
        data: totalCreateParis,
        lineStyle: {
            color:'#AF5AFF'
        },
        itemStyle: {
            color: '#AF5AFF'
        }
    },
    {
        name: '<?php echo $lang->report->totalActivate ?>',
        type: 'line',
        data: totalActivatedParis,
        lineStyle: {
            color:'#FFC73A'
        },
        itemStyle: {
            color: '#FFC73A'
        }
    },
    {
        name: '<?php echo $lang->report->totalToClose ?>',
        type: 'line',
        data: totalToCloseParis,
        lineStyle: {
            color:'#FF5A61'
        },
        itemStyle: {
            color: '#FF5A61'
        }
    },
    {
        name: '<?php echo $lang->report->totalToResolve ?>',
        type: 'line',
        data: totalToResolveParis,
        lineStyle: {
            color:'#50C8D0'
        },
        itemStyle: {
            color: '#50C8D0'
        }
    }
  ]
};

if (option && typeof option === 'object') {
  myChartTotal.setOption(option);
}

window.addEventListener('resize', myChartTotal.resize);
</script>

<script>
    function onExportToExcel(){
        var chartMode = $('.chart-mode.btn-primary').data('chart-mode');
        var begin = $('#begin').val();
        var end = $('#end').val();
        var project = $('#project').val();
        var testtask = $('#testtask').val();

        begin = begin.replace(/-/g, '/');
        end = end.replace(/-/g, '/');

        var projectStr = '';
        if(project != null)
        {
            projectStr = project.join(',');
        }

        var testtaskStr = '';
        if(testtask != null)
        {
            testtaskStr = testtask.join(',');
        }

        var link = createLink('report', 'bugtrendexport', 'queryType=xls&chartMode=' + chartMode + '&begin=' + begin + '&end=' + end + '&project=' + projectStr + '&testtask=' + testtaskStr);

        window.open(link);
    }

    function onExportToImage(){
        var chart1Image = myChart.getDataURL({
            pixelRatio: 2,
            backgroundColor: '#fff'
        });
        var chart2Image = myChartTotal.getDataURL({
            pixelRatio: 2,
            backgroundColor: '#fff'
        });

        var titleSize = 30;
        var title1 = bugTrend
        var title2 = totalBugTrend

        var img1 = new Image();
        var img2 = new Image();
        img1.src = chart1Image;
        img2.src = chart2Image;

        img1.onload = function(){
            img2.onload = function(){
                var img1Width = img1.width;
                var img1Height = img1.height;
                var img2Width = img2.width;
                var img2Height = img2.height;

                var canvas = document.createElement('canvas');
                var ctx = canvas.getContext('2d');
                canvas.width = img1Width;
                canvas.height = img1Height + img2Height + titleSize * 8;

                ctx.fillStyle = '#fff';
                ctx.fillRect(0, 0, canvas.width, canvas.height);

                ctx.font = titleSize + 'px Arial';
                ctx.textAlign = 'left';
                ctx.fillStyle = '#000';

                ctx.fillText(title1, 10, titleSize * 2);
                ctx.drawImage(img1, 0, titleSize * 4, img1Width, img1Height);

                ctx.fillText(title2, 10, img1Height + titleSize * 6);
                ctx.drawImage(img2, 0, img1Height + titleSize * 8, img2Width, img2Height);

                var image = canvas.toDataURL('image/png');
                var a = document.createElement('a');
                a.href = image;
                a.download = 'bugTrend.png';
                a.click();
            }
        }
    }
</script>
<?php endif;?>
<?php include '../../common/view/footer.html.php';?>
