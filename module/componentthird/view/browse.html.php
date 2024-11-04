<?php include '../../common/view/header.html.php'; ?>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <?php
        foreach ($labelList as $label => $labelName) {
            $active = $browseType == $label ? 'btn-active-text' : '';
            echo html::a($this->createLink('componentthird', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), '<span class="text">' . $labelName . '</span>', '', "class='btn btn-link $active'");
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
                $class = common::hasPriv('componentthird', 'export') ? '' : "class=disabled";
                $misc  = common::hasPriv('componentthird', 'export') ? "data-toggle='modal' data-type='iframe' class='export'" : "class=disabled";
                $link  = common::hasPriv('componentthird', 'export') ? $this->createLink('componentthird', 'export', "orderBy=$orderBy&browseType=$browseType") : '#';
                echo "<li $class>" . html::a($link, $lang->componentthird->export, '', $misc) . "</li>";
                ?>
            </ul>
        <?php if (common::hasPriv('componentthird', 'create')) echo html::a($this->createLink('componentthird', 'create'), "<i class='icon-plus'></i> {$lang->componentthird->create}", '', "class='btn btn-primary'"); ?>
        </div>
    </div>
</div>

<div id='mainContent' class='main-row'>
    <div class='main-col'>
        <div class="cell<?php if ($browseType == 'bysearch') echo ' show'; ?>" id="queryBox"
             data-module='componentthird'></div>
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
                        <th class='w-60px'><?php echo $lang->componentthird->code; ?></th>
                        <th class='w-120px'><?php common::printOrderLink('name', $orderBy, $vars, $lang->componentthird->name); ?></th>
                        <th class='w-60px'><?php common::printOrderLink('status', $orderBy, $vars, $lang->componentthird->status); ?></th>
                        <th class='w-100px'><?php common::printOrderLink('baseline', $orderBy, $vars, $lang->componentthird->baseline); ?></th>
                        <th class='w-100px'><?php common::printOrderLink('recommendVersion', $orderBy, $vars, $lang->componentthird->recommendVersion); ?></th>
                        <!--<th class='w-100px'><?php /*common::printOrderLink('versionDate', $orderBy, $vars, $lang->componentthird->versionDate); */?></th>-->
                        <th class='w-80px'><?php common::printOrderLink('category', $orderBy, $vars, $lang->componentthird->category); ?></th>
<!--                        <th class='w-160px'>--><?php //common::printOrderLink('chineseClassify', $orderBy, $vars, $lang->componentthird->chineseClassify); ?><!--</th>-->
                        <th class='w-160px'><?php common::printOrderLink('englishClassify', $orderBy, $vars, $lang->componentthird->englishClassify); ?></th>
                        <th class='w-80px'><?php common::printOrderLink('licenseType', $orderBy, $vars, $lang->componentthird->licenseType); ?></th>
                        <th class='w-60px'><?php common::printOrderLink('developLanguage', $orderBy, $vars, $lang->componentthird->developLanguage); ?></th>

                        <th class='w-60px'><?php echo $lang->componentthird->usedNum; ?></th>
                        <th class='text-center w-120px'><?php echo $lang->actions; ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($datas as $data): ?>
                        <tr>
                            <td class='text-ellipsis' title="<?php echo $data->code;?>"><?php echo $data->code;?></td>
                            <td class='text-ellipsis' title="<?php echo $data->name;?>"><?php echo common::hasPriv('componentthird', 'view') ? html::a(inlink('view', "componentthirdId=$data->id"), $data->name) : $data->name;?></td>
                            <td class='text-ellipsis' title="<?php echo zget($lang->component->thirdStatusList,$data->status); ?>"><?php echo zget($lang->component->thirdStatusList,$data->status);?></td>
                            <td class='text-ellipsis' title="<?php echo $data->baseline;?>"><?php echo $data->baseline;?></td>
                            <td class='text-ellipsis' title="<?php echo $data->recommendVersion;?>"><?php echo $data->recommendVersion;?></td>
                           <!-- <td class='text-ellipsis' title="<?php /*echo $data->versionDate;*/?>"><?php /*echo $data->versionDate;*/?></td>-->
                            <td class='text-ellipsis' title="<?php echo zget($lang->component->thirdcategoryList,$data->category); ?>"><?php echo zget($lang->component->thirdcategoryList,$data->category);?></td>
<!--                            <td class='text-ellipsis' title="--><?php //echo zget($lang->component->chineseClassifyList,$data->chineseClassify); ?><!--">--><?php //echo zget($lang->component->chineseClassifyList,$data->chineseClassify);?><!--</td>-->
                            <td class='text-ellipsis' title="<?php echo zget($lang->component->englishClassifyList,$data->englishClassify); ?>"><?php echo zget($lang->component->englishClassifyList,$data->englishClassify);?></td>
                            <td class='text-ellipsis' title="<?php echo $data->licenseType;?>"><?php echo $data->licenseType;?></td>
                            <td class='text-ellipsis' title="<?php echo zget($lang->component->developLanguageList,$data->developLanguage); ?>"><?php echo zget($lang->component->developLanguageList,$data->developLanguage);?></td>
                            <td class='text-ellipsis' title="<?php echo $data->usedNum;?>"><?php echo common::hasPriv('componentpublicaccount', 'browse') ? html::a($this->createLink('componentthirdaccount', 'browse', "browseType=componentReleaseId&param=$data->id"), $data->usedNum) : $data->usedNum;?></td>

                            <td class='c-actions text-center' style="overflow:visible">
                                <?php
                                common::printIcon('componentthird', 'edit', "componentthirdID=$data->id", $data, 'list', 'edit');
                                common::printIcon('componentthird', 'delete', "componentthirdID=$data->id", $data, 'list', 'trash', '', 'iframe', true);
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
