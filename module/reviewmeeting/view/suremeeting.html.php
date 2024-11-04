<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/ext/view/calendar.html.php';?>

<style>
    .calendar .cell-day.with-plus-sign .day>.heading:before{
        content: none;
    }
    .eventreview{
        overflow: hidden;
        text-overflow: ellipsis;
        position: relative;
        width: auto!important;
        padding: 0 5px;
        margin: 0 0 1px;
        font-size: 12px;
        line-height: 18px;
        background-color: transparent;
        cursor: pointer;
    }

</style>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <?php foreach($lang->reviewmeeting->suremeet as $key => $label):?>
            <?php $active = $status == $key ? 'btn-active-text' : '';?>
            <?php $label = "<span class='text'>$label</span>";?>
            <?php if($status == $key) {$label .= " <span class='label label-light label-badge'>{$meetCount[$key]}</span>";}?>
            <?php echo html::a(inlink('nomeet', "type=$key"), $label, '', "class='btn btn-link $active'");?>
        <?php endforeach;?>
    </div>
</div>
<div id="mainContent" class="main-row fade">
    <div class='main-col'>
            <div class="cell">
                <div style="color: lightslategray"></div>
                <div id="dutyCalendar" class="calendar">
                    <header class="calender-header table-row">
                        <div class="btn-toolbar col-4 table-col text-middle">
                            <button type="button" class="btn btn-info btn-icon btn-mini btn-prev"><i class="icon-chevron-left"></i></button>
                            <button type="button" class="btn btn-info btn-mini btn-today"><?php echo $lang->today;?></button>
                            <button type="button" class="btn btn-info btn-icon btn-mini btn-next"><i class="icon-chevron-right"></i></button>
                            <span class="calendar-caption"></span>
                        </div>
                    </header>
                </div>
            </div>
    </div>
</div>
<script>
    config.ajaxGetMeetUrl   = '<?php echo $this->createLink('reviewmeeting', 'ajaxGetMeetList', "year={year}");?>';
    config.meetViewUrl      = '<?php echo $this->createLink('reviewmeeting', 'meetingview', 'id={id}', '', true);?>';
    config.reviewViewUrl    = '<?php echo $this->createLink('reviewmeeting', 'reviewview', 'id={id}', '', true);?>';
    config.textNetworkError = '<?php echo $lang->textNetworkError;?>';
    config.textHasMoreItems = '<?php echo $lang->reviewmeeting->textMoreItems/*$lang->textHasMoreItems*/;?>';
</script>
<script>
    var dutyModalTrigger = new $.zui.ModalTrigger(
        {
            width: '70%',
            type: 'iframe',
            rememberPos: 'dutyViewModal',
            waittime: 5000
        });

    var batchAddModalTrigger = new $.zui.ModalTrigger(
        {
            width: '80%',
            type: 'iframe',
            waittime: 5000
        });

    var displayDate = 0;
    var calendar    = false;
    $(function()
    {
        var expandedDays   = {};
        var minExpandCount = 4;
        var $calendar      = $('#dutyCalendar');
        var toggleLoading  = function(loading)
        {
            $calendar.toggleClass('loading', !!loading);
        };
        calendar = $calendar.calendar(
            {
                dragThenDrop: false,
                hideEmptyWeekends: true,
                data:
                    {
                        events: [],
                        calendars:
                            {
                                defaultCal: {color: '#fff'}
                            }
                    },
                beforeDisplay: function(display, doDisplay) {
                    var date = display.date;
                    var thisDisplayDate = date.getFullYear();
                    if(displayDate === thisDisplayDate)
                    {
                        return doDisplay();
                    }
                    else
                    {
                        displayDate = thisDisplayDate;
                    }

                    var calendar = this;
                    toggleLoading(true);
                    $.ajax(
                        {
                            url: config.ajaxGetMeetUrl.replace('{year}', date.getFullYear()),
                            dataType: 'json',
                            success: function(data)
                            {
                                calendar.resetData({events: data});
                                doDisplay();
                            },
                            error: function()
                            {
                                $.zui.messager.danger(config.textNetworkError);
                            },
                            complete: function() {toggleLoading(false);}
                        });
                    return false;
                },
                eventCreator: function(event, $cell, calendar)
                {
                    var style = '';
                    var childstyle ='';
                    if(event.status == 'pass'){
                        style = 'style="color:LightGrey"';
                        childstyle = 'style="color:LightGrey"';
                    }else{
                        style = 'style="color:blue"';
                        childstyle ='style="color:#1183fb"';
                    }
                    var count =(event.list).length;
                    var $event = ('<div title="' + event.typeName + "（"+count+"）" +"_"+event.meetingPlanTime+ "（"+event.meetingPlanExport+"）" + '" data-id="' + (event.id || '') + '" class="event" >' + '<span class="title"  '+style+'><b>' + event.typeName + "（"+count+"）" +"_"+event.meetingPlanTime+ '</b></span>' + (event.improtantTime == '1' ? "<span><i class='icon icon-flag red' style='font-size: 14px'></i></span>" : '') + '</div>');
                    var $child = "";
                    for(var i = 0;i < (event.list).length;i++){
                        $child += ('<div title="' + event.list[i].createdDept+'/'+event.list[i].title+"（"+event.list[i].meetingPlanExportName+"）" + '"  class="eventreview"   onclick="getReview('+event.list[i].review_id+')"  style="margin-left:25px" id="'+event.list[i].review_id+'">' + '<span class="title" '+childstyle+'>' + event.list[i].createdDept+'/'+event.list[i].title+"（"+event.list[i].meetingPlanExportName+"）" +  '</span></div>');

                    }
                    return $($event + $child);
                },
                dayFormater: function($cell, date, dayEvents, calendar)
                {
                    if(dayEvents && dayEvents.maxPos >= minExpandCount)
                    {
                        var hideManyEvents = !expandedDays[date.toDateString()];
                        $cell.toggleClass('hide-many-events', hideManyEvents);
                        if(hideManyEvents)
                        {
                            var $cellContent = $cell.find('.day > .content');
                            var $showMore = $cellContent.find('.show-more-events');
                            if(!$showMore.length)
                            {
                                $showMore = $('<div class="show-more-events" />').appendTo($cellContent);
                            }
                            else
                            {
                                $showMore.show();
                            }
                            //  $showMore.text(config.textHasMoreItems.format(dayEvents.maxPos - minExpandCount + 1));
                            $showMore.text(config.textHasMoreItems);
                        }
                    }
                    else
                    {
                        $cell.removeClass('hide-many-events');
                    }
                    if($cell.is('.future')) $cell.addClass('with-plus-sign');
                },
            }).data('zui.calendar');
        $calendar.on('click', '.show-more-events', function(e)
        {
            var $cell = $(this).hide().closest('.cell-day');
            $cell.removeClass('hide-many-events');
            expandedDays[$cell.find('.day').attr('data-date')] = true;
            e.stopPropagation();
        }).on('click', '.event', function(e)
        {
            var event = $(this).data('event');
            dutyModalTrigger.show({url: config.meetViewUrl.replace('{id}', event.id)});
            e.stopPropagation();
        });
    });
    function getReview(id){
        dutyModalTrigger.show({url: config.reviewViewUrl.replace('{id}', id)});
        e.stopPropagation();
    }
</script>
<?php include '../../common/view/footer.html.php';?>
