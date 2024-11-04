<style>
.ke-icon-holder {width: 20px; background: none; text-align: center; background-color: #0C64EB; line-height: 14px;border-radius:4px;}
.ke-icon-holder:before {content: 'M'; font-size: 13px; font-weight: normal; color: #fff;}
</style>
<div class="modal fade" id="holderModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">
          <span aria-hidden="true">×</span><span class="sr-only"><?php $lang->close;?></span>
        </button>
        <h4 class="modal-title" id='holderModalTitle'><?php echo $lang->measurement->addMeas;?></h4>
      </div>
      <div class="modal-body">
        <table class='table table-form'>
          <tr>
            <th class='w-100px'><?php echo $lang->measurement->type;?></th>
            <td><?php echo html::select('type', $lang->measurement->sysData, '', "class='form-control chosen'");?></td>
          </tr>
          <tr id='basicMeasBox'>
            <th class='w-100px'><?php echo $lang->measurement->measList;?></th>
            <td><?php echo html::select('basicMeas', $basicMeases, '', "class='form-control chosen'");?></td>
          </tr>
          <tr id='reportBox' class='hidden'>
            <th class='w-100px'><?php echo $lang->measurement->reportList;?></th>
            <td><?php echo html::select('report', $reports, '', "class='form-control chosen'");?></td>
          </tr>
          <tr>
            <td colspan='2' class='from-actions text-center'>
              <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $lang->close;?></button>
              <button type="button" class="btn btn-primary" id='holderSaveBtn'><?php echo $lang->save;?></button>
            </td>
          </tr>
        </table>

        <div class="form-group">
          <input id="holderText" type="text" class="form-control">
        </div>
        <div class="form-group">
          <textarea class="form-control" name="holderValue" id="holderValue" cols="30" rows="2" readonly=true></textarea>
        </div>
        <div class="form-group hidden">
          <div class="checkbox">
            <label>
              <input type="checkbox" id="holderAsBlock">
            </label>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
function afterHolderShow(value)
{
    if(typeof(window.holder) == 'undefined') window.holder = {};

    if(value.indexOf('measurement') == 1)
    {
        params = value.split(':');
        params = params[1].split('.');
        type   = params[0];
        window.holder.measurement = params[1].split('}')[0];
    }

    if(value.indexOf('report') == 1)
    {
        type = 'report';
        params = value.split(':');
        params = params[1].split('.');
        window.holder.reportType = params[0];
        window.holder.reportID   = params[1].split('}')[0];
    }

    $('#type').val(type).change();
}
$(document).on('change', '#type', function()
{
    var type = $('#type').val();
    if(type == 'basic')
    {
        $('#basicMeasBox').removeClass('hidden');
        $('#reportBox').addClass('hidden');
        $('#holderAsBlock').prop('checked', false);
    }
    else
    {
        $('#basicMeasBox').addClass('hidden');
        $('#reportBox').removeClass('hidden');
        $('#holderAsBlock').prop('checked', true);
    }

    if(typeof(window.holder.measurement) != 'undefined')
    {
        if(type == 'basic')  $('#basicMeas').val(window.holder.measurement).change().trigger('chosen:updated');
    }
})

$(document).on('change', '#basicMeas, #report', function()
{
    var type       = $('#type').val();
    var holderText = $(this).find('option:selected').text();
    var uniqid     = getUniqid(8);

    if(type == 'basic')
    {
        var measurement = $(this).val();
        var holderValue = '{measurement_' + uniqid + ':' + type + '.' + measurement + '}';
    }
    else
    {
        var reportType  = 'cmmi';
        var reportID    = $(this).val();
        var holderValue = '{report_' + uniqid + ':' + reportType + '.' + reportID + '}';
    }

    $('#holderText').val(holderText);
    $('#holderValue').val(holderValue);
})

function getUniqid(length)
{
    return Number(Math.random().toString().substr(3,length) + Date.now()).toString(36);
}
</script>
