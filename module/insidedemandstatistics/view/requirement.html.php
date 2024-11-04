<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<style>.body-modal #mainMenu > .btn-toolbar .page-title {
        width: auto;
    }</style>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <div class="page-title">
            <span class="text">需求任务统计表</span>
        </div>
    </div>
</div>
<div id='mainContent' class='main-row'>
    <div class='side-col' id='sidebar'>
        <?php include 'blockreportlist.html.php';?>
    </div>
    <div class='main-col'>

        <div class='cell'>
            <div class='panel'>

                <div data-ride='table'>
                    <table class='table table-condensed table-striped table-bordered table-fixed no-margin' id='productList'>
                        <thead >
                        <tr class="text-center">
                            <th class='w-200px'>需求意向主题</th>
                            <th class='w-100px'>未交付</th>
                            <th class='w-100px'>已交付</th>
                            <th class="w-100px">投产成功</th>
                            <th class="w-100px">合计</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $color = false;
                        $totalWait = 0;
                        $totalDelivery = 0;
                        $totalOnline = 0;
                        $totalAll = 0;
                        foreach ($list as $id =>$item) {
                            ?>

                            <tr class="text-center">
                                <?php $class = $color ? 'rowcolor' : '';?>
                                <td class="<?php echo $class;?>"><?php echo zget($opinions, $id);?></td>
                                <td class="<?php echo $class;?>"><?php echo $item->wait; $totalWait += $item->wait; ?></td>
                                <td class="<?php echo $class;?>"><?php echo $item->delivered; $totalDelivery += $item->delivered; ?></td>
                                <td class="<?php echo $class;?>"><?php echo $item->onlined; $totalOnline += $item->onlined; ?></td>
                                <td class="<?php echo $class;?>"><?php echo $total = ($item->wait + $item->delivered + $item->onlined); $totalAll += $total; ?></td>
                            </tr>
                        <?php } ?>

                        <tr class="text-center">
                            <?php $class = $color ? 'rowcolor' : '';?>
                            <td class="<?php echo $class;?>">合计</td>
                            <td class="<?php echo $class;?>"><?php echo $totalWait;?></td>
                            <td class="<?php echo $class;?>"><?php echo $totalDelivery;?></td>
                            <td class="<?php echo $class;?>"><?php echo $totalOnline;?></td>
                            <td class="<?php echo $class;?>"><?php echo $totalAll;?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
<?php include '../../common/view/footer.html.php'; ?>
