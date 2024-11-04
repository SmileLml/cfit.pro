<?php include '../../common/view/header.html.php';?>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->publiccomponetcollect->tip ;?></h2>
        </div>
            <div>
                <b><?php echo $lang->publiccomponetcollect->tipDesc ;?></b>
                <div class = "btn-toolbar pull-right">
                    <?php echo html::a('','','','data-app="componentmanage"  id="urlclick" type="hidden"')?>
                    <?php echo html::a('javascript:void(0)', $lang->publiccomponetcollect->look , '', 'class="btn btn-primary"  onclick = "urlclick()"')?>
                </div>
            </div>
    </div>
</div>
<script>
    function urlclick(){
        var url = createLink('cjdpf','browse');
        $('#urlclick').attr('href',url);
        $('#urlclick')[0].click();
    }
</script>
<?php include '../../common/view/footer.html.php';?>

