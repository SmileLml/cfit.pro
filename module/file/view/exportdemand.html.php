<?php
/**
 * The export view file of file module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Congzhi Chen <congzhi@cnezsoft.com>
 * @package     file
 * @version     $Id$
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.lite.html.php';?>
<?php include '../../common/view/chosen.html.php';?>
<?php $this->app->loadLang('file');?>
<style>
    #customFields .panel {border: 1px solid #ddd; background: #fafafa; margin: 0;max-height: 400px;overflow-x: scroll;}
    #customFields .panel-actions {padding: 0;}
    #customFields .panel {position: relative;}
    #customFields .panel:before, #customFields .panel:after {content: ' '; display: block; width: 0; height: 0; border-style: solid; border-width: 0 10px 10px 10px; border-color: transparent transparent #f1f1f1 transparent; position: absolute; left: 315px; top: -9px;}
    #customFields .panel:before {border-color: transparent transparent #ddd transparent; top: -10px;}
    /*左右穿梭框+拖拽排序*/
    .select_div{background-color: #fff;padding: 8px;margin-bottom: 5px;border: 1px solid #eee;}
    .select_left,.select_right{float: left;width:46%;border: 1px solid #eee;box-sizing: border-box;padding: 5px 0 5px 0px}
    /*.select_right{margin-left: 8%;}*/
    .select_div>div:nth-child(1){margin-right: ;}
    .select_div>div>div{padding-left:6px;}
    .select_d_first{border-bottom: 1px solid #eee;margin-bottom: 5px}
    .select_center{width:8%;float:left;margin-top: 80px;transform: translateZ(0);}
    .select_center>div{padding-left: 0 !important;text-align: center;margin-top: 10px}
    .select_center i{cursor: not-allowed}
    .list-group .list-group-item{cursor:move}
    .list-group input[type="checkbox" i]{cursor: move;}
    /*多选框label标签高度自适应，文本自动换行*/
    .checkbox-primary>label, .radio-primary>label{height:auto !important;}
</style>
<script>
    function setDownloading()
    {
        $("[name='exportFields[]']").attr("checked",true);
        if(navigator.userAgent.toLowerCase().indexOf("opera") > -1) return true;   // Opera don't support, omit it.

        var $fileName = $('#fileName');
        if($fileName.val() === '') $fileName.val('<?php echo $lang->file->untitled;?>');

        $.cookie('downloading', 0);
        time = setInterval("closeWindow()", 300);
        $('#mainContent').addClass('loading');
        return true;
    }

    function closeWindow()
    {
        if($.cookie('downloading') == 1)
        {
            $('#mainContent').removeClass('loading');
            parent.$.closeModal();
            $.cookie('downloading', null);
            clearInterval(time);
        }
    }
    function switchEncode(fileType)
    {
        var $encode = $('#encode');
        if(fileType != 'csv') $encode.val('utf-8').attr('disabled', 'disabled');
        else $encode.removeAttr('disabled');
        $encode.trigger('chosen:updated');
    }
    function saveTemplate()
    {
        var $inputGroup = $('#customFields div.input-group');
        var $publicBox  = $inputGroup.find('input[id^="public"]');
        var title       = $inputGroup.find('#title').val();
        // var content     = $('#customFields #exportFields').val();
        var content = "";
        $("[name='exportFields[]']").each(function () {
            content += $(this).val()+',';
        });
        content = content.slice(0,-1);
        // data-toggle="tooltip" data-tip-class="tooltip-success"

        var isPublic    = ($publicBox.size() > 0 && $publicBox.prop('checked')) ? $publicBox.val() : 0;
        if(!title || !content){
            bootbox.alert("模板内容不能为空");
            return false;
        }
        var isCreated = 0;
        $("#template option").each(function () {
            if (title == $(this).text()){
                isCreated = 1;
            }
        })
        if (isCreated == 1){
            bootbox.alert("模板名称已经存在，请重新填写模板名称。");
            return false;
        }

        saveTemplateLink = '<?php echo $this->createLink('file', 'ajaxSaveTemplate', 'module=' . $this->moduleName);?>';
        $.post(saveTemplateLink, {title:title, content:content, public:isPublic}, function(data)
        {
            var defaultValue = $('#tplBox #template').val();
            $('#tplBox').html(data);
            if(data.indexOf('alert') >= 0) $('#tplBox #template').val(defaultValue);
            $("#tplBox #template").chosen().on('chosen:showing_dropdown', function()
            {
                var $this = $(this);
                var $chosen = $this.next('.chosen-container').removeClass('chosen-up');
                var $drop = $chosen.find('.chosen-drop');
                $chosen.toggleClass('chosen-up', $drop.height() + $drop.offset().top - $(document).scrollTop() > $(window).height());
            });
            $inputGroup.find('#title').val(title);
            $('#saveTpl').tooltip('show','模板保存成功');
        });
    }

    /* Set template. */
    function setTemplate(templateID)
    {
        var $template=  $('#tplBox #template' + templateID);
        var exportFields = $template.size() > 0 ? $template.html() : defaultExportFields;
        exportFields = exportFields.split(',');
        $('#exportFields').val('');
        var str = "";
        for(i in exportFields)
        {
            str += '<div class="list-group-item">\n' +
                ' <label class="checkbox-primary" ><input type="checkbox" class="selectedFiled" name="exportFields[]" onchange="checkOne(\'selected\',\'selectedFiled\')" value="'+exportFields[i]+'" id="selectedFileds_'+exportFields[i]+'"> <label for="selectedFileds_'+exportFields[i]+'">'+exportFieldPairs[exportFields[i]]+'</label></label>\n' +
                '</div>'
        }
        var noSelect = "";
        for(j in exportFieldPairs){
            if ($.inArray(j,exportFields) == -1){
                noSelect += '<div>\n' +
                    ' <label class="checkbox-primary" ><input type="checkbox" class="allFiled" name="allFileds[]" onchange="checkOne(\'check-all\',\'allFiled\')" value="'+j+'" id="filedName_'+j+'"> <label for="filedName_'+j+'" >'+exportFieldPairs[j]+'</label></label>\n' +
                    '</div>'
            }
        }
        $("#select_left").empty().append(noSelect);
        $("#sortableList").empty().append(str);
    }

    /* Delete template. */
    function deleteTemplate()
    {
        bootbox.confirm('确认是否删除模板', function (result){
            if((result)){
                var templateID = $('#tplBox #template').val();
                if(templateID == 0) return;
                hiddenwin.location.href = createLink('file', 'ajaxDeleteTemplate', 'templateID=' + templateID);
                $('#tplBox #template').find('option[value="'+ templateID +'"]').remove();
                $('#tplBox #template').trigger("chosen:updated");
                $('#tplBox #template').change();
            }
        });

    }

    /**
     * Toggle export template box.
     *
     * @access public
     * @return void
     */
    function setExportTPL()
    {
        $('#customFields').toggleClass('hidden');
        $(".tipsShow").slideToggle();
    }

    $(document).ready(function()
    {
        $(document).on('change', '#template', function()
        {
            $('#title').val($(this).find('option:selected').text());
        });

        $('#fileType').change();
        <?php if($this->cookie->checkedItem):?>
        setTimeout(function()
        {
            $('#exportType').val('selected').trigger('chosen:updated');
        }, 150);
        <?php endif;?>

        if($('#customFields #exportFields').length > 0)
        {
            $('#customFields #exportFields').change(function()
            {
                setTimeout(function()
                {
                    var optionHtml = '';
                    var selected   = ',';
                    $('#customFields #exportFields_chosen .chosen-choices li.search-choice').each(function(i)
                    {
                        index = $(this).find('.search-choice-close').data('option-array-index');
                        optionHtml += $('#exportFields option').eq(index).attr('selected', 'selected').prop("outerHTML");
                        $(this).find('.search-choice-close').attr('data-option-array-index', i);
                        selected += index + ',';
                    })
                    $('#exportFields option').each(function(i)
                    {
                        if(selected.indexOf(',' + i + ',') < 0) optionHtml += $(this).removeAttr('selected').prop("outerHTML");
                    })
                    $('#exportFields').html(optionHtml).trigger('chosen:updated');
                }, 100);
            })
        }
    });
</script>

<main id="main">
    <div class="container">
        <div id="mainContent" class='main-content load-indicator'>
            <div class='main-header'>
                <h2><?php echo $lang->export;?></h2>
            </div>
            <form class='main-form' method='post' target='hiddenwin'>
                <table class="table table-form">
                    <tbody>
                    <tr>
                        <th class='w-120px'><?php echo $lang->file->fileName;?></th>
                        <td class="w-300px"><?php echo html::input('fileName', isset($fileName) ? $fileName : '', "class='form-control' autofocus placeholder='{$lang->file->untitled}'");?></td>
                        <td></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->file->extension;?></th>
                        <td><?php echo html::select('fileType', $lang->exportFileTypeList, '', 'onchange=switchEncode(this.value) class="form-control"');?></td>
                    </tr>
                    <tr class="tipsShow" style="display: none;">
                    <tr>
                        <th></th>
                        <td>
                            <?php echo html::submitButton($lang->export, "onclick='setDownloading();'", 'btn btn-primary');?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
</main>
<?php include '../../common/view/footer.lite.html.php';?>
<script>
    $(function () {
        var obj = $(window.parent.document).find('input[type="checkbox"]');
        var selectRes = false;
        obj.each(function () {
            if ($(this).is(":checked")){
                selectRes = true;
            }
        })
        if (!selectRes){
            $("#exportType option").each(function () {
                if ($(this).val() == 'selected'){
                    $(this).remove();
                }
            })
        }
    })
    $("#public1").change(function () {
        $(".publicTxt").slideToggle();
    })
    $(".panel").scroll(function () {
        var _top = $(this).scrollTop();
        $(".select_center").css("margin-top",_top+80+'px')
    })
    function checkAll(allClass,_class,btn) {
        var res = false;
        if ($("."+allClass).is(":checked")){
            res = true;
        }
        $("."+_class).each(function () {
            $(this).attr("checked",res);
        })
        if (res){
            $("."+btn).children("i").css("cursor",'pointer');
        }else{
            $("."+btn).children("i").css("cursor",'not-allowed');
        }
    }
    function checkOne(allClass,_class) {
        var res = true;
        var status = 0;
        $("."+_class).each(function () {
            if(!$(this).is(":checked")){
                res = false
            }else{
                status = 1;
            }
        })
        var btnClass = "right_btn";
        if(allClass == 'selected'){
            btnClass = "left_btn";
        }
        if (status == 1){
            $("."+btnClass).children("i").css("cursor",'pointer');
        }else{
            $("."+btnClass).children("i").css("cursor",'not-allowed');
        }
        $("."+allClass).attr("checked",res);
    }
    function leftAdd() {
        var _key = "";
        var _val = "";
        var info = {};
        var str = "";
        $(".selectedFiled").each(function () {
            if($(this).is(":checked")){
                _key = $(this).val();
                _val = $(this).siblings("label").text();
                info[_key] = _val;
                str += '<div>\n' +
                    ' <label class="checkbox-primary" ><input type="checkbox" class="allFiled" name="allFileds[]" onchange="checkOne(\'check-all\',\'allFiled\')" value="'+_key+'" id="filedName_'+_key+'"> <label for="filedName_'+_key+'" >'+_val+'</label></label>\n' +
                    '</div>'
                $(this).parent().parent().remove();
            }
        })
        $("#select_left").append(str);
        $(".selected").attr("checked",false);
        $(".right_btn").children("i").css("cursor",'not-allowed');
        $(".left_btn").children("i").css("cursor",'not-allowed');
    }
    function rightAdd() {
        var _key = "";
        var _val = "";
        var info = {};
        var str = "";
        $(".allFiled").each(function () {
            if($(this).is(":checked")){
                _key = $(this).val();
                _val = $(this).siblings("label").text();
                info[_key] = _val;
                str += '<div class="list-group-item">\n' +
                    ' <label class="checkbox-primary" ><input type="checkbox" class="selectedFiled" name="exportFields[]" onchange="checkOne(\'selected\',\'selectedFiled\')" value="'+_key+'" id="selectedFileds_'+_key+'"> <label for="selectedFileds_'+_key+'">'+_val+'</label></label>\n' +
                    '</div>'
                $(this).parent().parent().remove();
            }
        })
        $("#sortableList").append(str);
        $(".check-all").attr("checked",false);
        $(".right_btn").children("i").css("cursor",'not-allowed');
        $(".left_btn").children("i").css("cursor",'not-allowed');
        var res = true;
        $(".selectedFiled").each(function () {
            if(!$(this).is(":checked")){
                res = false
            }
        })
        $(".selected").attr("checked",res);
        initSort();
    }
    function initSort() {
        var options = {
            selector: '.list-group-item',
            stopPropagation:true,
            finish:function(e){
                $(".list-group-item").each(function (i) {
                    if ($(this).hasClass('drag-from')) $(this).removeClass("drag-from");
                    if ($(this).hasClass('dragging')) $(this).removeClass("dragging");
                    if ($(this).hasClass('invisible')) $(this).removeClass("invisible");
                })
            }
        }
        $("#sortableList").sortable(options)
    }
    initSort();
</script>
