<?php include '../../common/view/header.html.php'; ?>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <?php
        $i = 0;
        foreach ($lang->residentsupport->temDeptStatusDescList as $label => $labelName) {
            $active = $browseType == strtolower($label)  ? 'btn-active-text' : '';
            echo html::a($this->createLink('residentsupport', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), '<span class="text">' . $labelName . '</span>', '', "class='btn btn-link $active'");

            $i++;
            if ($i >= 12) break;
        }
        if ($i >= 12) {
            echo "<div class='btn-group'><a href='javascript:;' data-toggle='dropdown' class='btn btn-link'>{$lang->more}<span class='caret'></span></a>";
            echo "<ul class='dropdown-menu'>";
            $i = 0;
            foreach ($lang->residentsupport->labelList as $label => $labelName) {
                $i++;
                if ($i <= 12) continue;

                $active = $browseType == $label ? 'btn-active-text' : '';
                echo '<li>' . html::a($this->createLink('residentsupport', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), "<span class='text'>{$labelName}</span>", '', "class='btn btn-link $active'") . '</li>';
            }
            echo '</ul></div>';
        }
        ?>
        <a class="btn btn-link querybox-toggle" id='bysearchTab'>
            <i class="icon icon-search muted"></i> <?php echo $lang->searchAB; ?></a>
    </div>
    <div class="btn-toolbar pull-right">
        <!--
        <div class='btn-group'>
        </div>
        -->
        <?php if (common::hasPriv('residentsupport', 'enableScheduling')) echo html::a($this->createLink('residentsupport', 'enableScheduling', ''), "<i class='icon-plus'></i> {$lang->residentsupport->enableScheduling}", '', "class='btn btn-primary' data-toggle='modal' data-type='iframe'"); ?>
    </div>
</div>

<div id='mainContent' class='main-row'>
    <div class='main-col'>
        <div class="cell<?php if ($browseType == 'bysearch') echo ' show'; ?>" id="queryBox"
             data-module='residentsupport'></div>
        <?php if (empty($reviewList)): ?>
            <div class="table-empty-tip">
                <p>
                    <span class="text-muted"><?php echo $lang->noData; ?></span>
                </p>
            </div>
        <?php
            else:
                //搜索条件
                $params = "browseType=$browseType&param=$param&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}&pageID={$pager->pageID}";
        ?>
            <form class='main-table' id='residentsupportForm' method='post' data-ride='table' data-nested='true'
                  data-checkable='false'>
                <?php $vars = "browseType=$browseType&param=$param&orderBy=%s&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=$pager->pageID"; ?>
                <?php include 'browserTemDeptList.html.php'; ?>
                <div class="table-footer">
                    <?php $pager->show('right', 'pagerjs'); ?>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>
<?php include '../../common/view/footer.html.php'; ?>
