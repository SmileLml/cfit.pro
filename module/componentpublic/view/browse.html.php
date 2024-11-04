<?php include '../../common/view/header.html.php'; ?>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <?php
        foreach ($lang->componentpublic->labelList as $label => $labelName) {
            $active = $browseType == $label ? 'btn-active-text' : '';
            echo html::a($this->createLink('componentpublic', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), '<span class="text">' . $labelName . '</span>', '', "class='btn btn-link $active'");
        }
        ?>
        <a class="btn btn-link querybox-toggle" id='bysearchTab'><i
                class="icon icon-search muted"></i> <?php echo $lang->searchAB; ?></a>
    </div>
    <div class="btn-toolbar pull-right">
        <div class='btn-group'>
        <button class="btn btn-link" data-toggle="dropdown"><i class="icon icon-export muted"></i> <span class="text"><?php echo $lang->export ?></span> <span class="caret"></span></button>
        <ul class="dropdown-menu" id='exportActionMenu'>
            <?php
            $class = common::hasPriv('componentpublic', 'export') ? '' : "class=disabled";
            $misc  = common::hasPriv('componentpublic', 'export') ? "data-toggle='modal' data-type='iframe' class='export'" : "class=disabled";
            $link  = common::hasPriv('componentpublic', 'export') ? $this->createLink('componentpublic', 'export', "orderBy=$orderBy&browseType=$browseType") : '#';
            echo "<li $class>" . html::a($link, $lang->componentpublic->export, '', $misc) . "</li>";
            ?>
        </ul>
        <?php if (common::hasPriv('componentpublic', 'create')) echo html::a($this->createLink('componentpublic', 'create'), "<i class='icon-plus'></i> {$lang->componentpublic->create}", '', "class='btn btn-primary'"); ?>

        </div>
    </div>
</div>

<div id='mainContent' class='main-row'>
    <div class='main-col'>
        <div class="cell<?php if ($browseType == 'bysearch') echo ' show'; ?>" id="queryBox"
             data-module='componentpublic'></div>
        <?php if (empty($datas)): ?>
            <div class="table-empty-tip">
                <p>
                    <span class="text-muted"><?php echo $lang->noData; ?></span>
                </p>
            </div>
        <?php else: ?>
            <form class='main-table' id='problemForm' method='post' data-ride='table' data-nested='true'
                  data-checkable='false'>
                <?php $vars = "browseType=$browseType&param=$param&orderBy=%s&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=$pager->pageID"; ?>
                <table class='table table-fixed has-sort-head' id='problems'>
                    <thead>
                    <tr>
                        <th class='w-60px'><?php echo $lang->componentpublic->code; ?></th>
                        <th class='w-120px'><?php common::printOrderLink('name', $orderBy, $vars, $lang->componentpublic->name); ?></th>
                        <th class='w-100px'><?php common::printOrderLink('latestVersion', $orderBy, $vars, $lang->componentpublic->latestVersion); ?></th>
                        <th class='w-80px'><?php common::printOrderLink('level', $orderBy, $vars, $lang->componentpublic->level); ?></th>
                        <th class='w-130px'><?php common::printOrderLink('category', $orderBy, $vars, $lang->componentpublic->category); ?></th>
                        <th class='w-300px'><?php echo $lang->componentpublic->functionDesc; ?></th>
                       <!-- <th class='w-160px'><?php /*echo $lang->componentpublic->location; */?></th>-->
                        <th class='w-80px'><?php common::printOrderLink('maintainer', $orderBy, $vars, $lang->componentpublic->maintainer); ?></th>
                        <th class='w-120px'><?php common::printOrderLink('maintainerDept', $orderBy, $vars, $lang->componentpublic->maintainerDept); ?></th>
                        <th class='w-60px'><?php common::printOrderLink('developLanguage', $orderBy, $vars, $lang->componentpublic->developLanguage); ?></th>
                        <th class='w-60px'><?php common::printOrderLink('status', $orderBy, $vars, $lang->componentpublic->status); ?></th>
                        <th class='w-60px'><?php echo $lang->componentpublic->usedNum; ?></th>
                        <th class='text-center w-120px'><?php echo $lang->actions; ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($datas as $data): ?>
                        <?php $data->functionDesc = htmlspecialchars_decode($data->functionDesc);    //处理【功能说明】中的图片和表格等
                        $data->functionDesc = str_replace("&nbsp;","",$data->functionDesc);//将空格替换成空
                        $data->functionDesc = strip_tags($data->functionDesc);//函数剥去字符串中的 HTML、XML 以及 PHP 的标签,获取纯文本内容
                        $data->functionDesc = mb_substr($data->functionDesc, 0, 100,"utf-8");?>
                        <tr>
                            <td class='text-ellipsis' title="<?php echo $data->code;?>"><?php echo $data->code;?></td>
                            <td class='text-ellipsis' title="<?php echo $data->name;?>"><?php echo common::hasPriv('componentpublic', 'view') ? html::a(inlink('view', "componentpublicId=$data->id"), $data->name) : $data->name;?></td>
                            <td class='text-ellipsis' title="<?php echo $data->latestVersion;?>"><?php echo $data->latestVersion;?></td>
                            <td class='text-ellipsis' title="<?php echo zget($lang->componentpublic->levelList,$data->level); ?>"><?php echo zget($lang->componentpublic->levelList,$data->level);?></td>
                            <td class='text-ellipsis' title="<?php echo zget($lang->component->categoryList,$data->category); ?>"><?php echo zget($lang->component->categoryList,$data->category);?></td>
                            <td class='text-ellipsis' title="<?php echo $data->functionDesc;?>"><?php echo $data->functionDesc;?></td>
                           <!-- <td class='text-ellipsis' title="<?php /*echo $data->location;*/?>"><?php /*echo $data->location;*/?></td>-->
                            <td class='text-ellipsis' title="<?php echo zget($users,$data->maintainer); ?>"><?php echo zget($users,$data->maintainer);?></td>
                            <td class='text-ellipsis' title="<?php echo zget($depts,$data->maintainerDept); ?>"><?php echo zget($depts,$data->maintainerDept);?></td>
                            <td class='text-ellipsis' title="<?php echo zget($lang->component->developLanguageList,$data->developLanguage); ?>"><?php echo zget($lang->component->developLanguageList,$data->developLanguage);?></td>
                            <td class='text-ellipsis' title="<?php echo zget($lang->component->publishStatusList,$data->status); ?>"><?php echo zget($lang->component->publishStatusList,$data->status);?></td>
                            <td class='text-ellipsis' title="<?php echo $data->usedNum;?>"><?php echo common::hasPriv('componentpublicaccount', 'browse') ? html::a($this->createLink('componentpublicaccount', 'browse', "browseType=componentId&param=$data->id"), $data->usedNum) : $data->usedNum;?></td>

                            <td class='c-actions text-center' style="overflow:visible">
                                <?php
                                common::printIcon('componentpublic', 'edit', "componentpublicID=$data->id", $data, 'list', 'edit');
                                common::printIcon('componentpublic', 'delete', "componentpublicID=$data->id", $data, 'list', 'trash', '', 'iframe', true);
                                echo html::a($this->createLink('componentpublic', 'demandAdvice', "componentpublicID=$data->id", '', false), "<i class='icon-hand-right'></i>" , '', "class='btn ' title='{$lang->componentpublic->demandAdvice}' ");
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="table-footer">
                    <?php $pager->show('right', 'pagerjs'); ?>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php include '../../common/view/footer.html.php'; ?>
