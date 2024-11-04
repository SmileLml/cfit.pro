<?php include '../../../common/view/header.html.php';?>
<style>
    ::-webkit-scrollbar{width:3px}
    .leftNav{width:18%;padding: 10px;box-sizing: border-box;border-right: 2px solid #eeeeff;max-height:500px;height: 500px;overflow-y: scroll;position: fixed;max-width:240px;background-color: #eee;padding: 10px }
    .leftNav>div{margin-bottom: 10px}
    .one-level{font-size: 16px;line-height: 24px;cursor: pointer;}
    .two-level{padding-left: 12px}
    .two-level>div{line-height: 24px;text-overflow: ellipsis;overflow: hidden;white-space: nowrap;cursor: pointer;margin-top:5px;padding-left:3px;
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;}
    .check-show{color:#cbd0db;border-radius: 2px;border:1px solid #cbd0db;float: right}
    .icon-hide:before {content: "\e926";}
    .check-active{font-weight: bold;background-color: #ccc;border-radius: 3px}
    .right{float: right;width:80%;margin-left: 2%;min-height: 500px}
    .nodata{text-align: center;margin-top: 100px;font-size: 36px}
    .fileslist{margin-top: 20px;}
    .fileslist>div:nth-child(1){font-size: 16px;margin: 8px 0;font-weight: bold}
    .one-level>div{max-width: 180px;text-overflow: ellipsis;overflow: hidden;white-space: nowrap;float: left}
    .one-level>i{float:right}
    .one-check{width:100%;background-color: #0b5ad3;padding:4px 0 4px 5px;box-sizing: border-box;color:#fff;border-radius: 5px}
</style>

<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->userUseDoc;?></h2>
        </div>
        <div class="clearfix">
            <div class="leftNav">
                <?php foreach ($data as $k=>$v) {?>
                    <div>
                        <div class="clearfix one-level <?php echo $v['type'];?>" data-key="<?php echo $v['type'];?>">
                            <div title="<?php echo $v['typeName'];?>"><?php echo $v['typeName'];?></div>
                            <i class="icon icon-sm icon-plus check-show"></i>
                        </div>
<!--                        --><?php //if(!empty($v['data'])):?>
                            <div class="two-level" style="display: none;">
                                <?php foreach ($v['data'] as $item):?>
                                <div data-key="<?php echo $item->id?>" title="<?php echo $item->name?>"><?php echo $item->name?></div>
                                <?php endforeach;?>
                            </div>
<!--                        --><?php //endif;?>
                    </div>
                <?php }?>

            </div>
            <div class="right">
                <div class="rightContent" style="max-width: 100%"></div>
            </div>
        </div>
    </div>
</div>
<script>
    $(".one-level").click(function () {
        if($(this).siblings().is(":hidden")){
            $(this).children("div").addClass('one-check')
            $(this).parent().siblings().children('.one-level').children("div").removeClass('one-check');
            $(this).siblings().slideDown();
            $(this).children("i").removeClass('icon-plus').addClass("icon-hide");
            $(this).parent().siblings().children(".two-level").slideUp();
            $(this).parent().siblings().children(".two-level").children("div").removeClass('check-active');
            $(this).parent().siblings().children(".one-level").children("i").removeClass('icon-hide').addClass("icon-plus");
            $(this).siblings().children().eq(0).addClass('check-active');
            //获取首个子元素
            var id = $(this).siblings().children().eq(0).attr('data-key');
            var moduleName = $(this).attr('data-key');
            getDoc(id);
        }else{
            $(this).siblings().slideUp();
            $(this).parent().siblings().children('.one-level').children("div").removeClass('one-check');
            $(this).siblings().children().removeClass('check-active');
            $(this).children("i").removeClass('icon-hide').addClass("icon-plus");
        }
    })
    var moduleName = "<?php echo $moduleName;?>";
    if(moduleName != ''){
        $("."+moduleName).click();
        var _top = $("." + moduleName).offset().top - $(".leftNav").offset().top + $(".leftNav").scrollTop() - 50;
        $(".leftNav").animate({
            'scrollTop'  : _top
        })
    }
    $(".two-level div").click(function () {
        $(this).addClass("check-active");
        var id = $(this).attr('data-key');
        $(this).siblings().removeClass("check-active");
        getDoc(id);
    })
    function getDoc(id) {
        var url = '<?php echo $this->createLink('my', 'ajaxgetDocContent', 'id=IDSTR', '', true);?>';
        $.get(url.replace('IDSTR',id),{},function (res) {
            $(".right").empty().append('<div class="rightContent" style="max-width: 100%">'+res.content+'</div>')
            if (res.filesHtmlTag != ''){
                $(".right").append('<div class="fileslist"><div>附件列表</div>'+res.filesHtmlTag+'</div>')
            }
        },'json')
    }
</script>
