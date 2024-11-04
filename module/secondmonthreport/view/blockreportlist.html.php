<div class="sidebar-toggle">
    <i class="icon icon-angle-left"></i>
</div>
<?php foreach ($this->lang->secondmonthreport->menuTitle as $key => $value): ?>
    <div class="cell">
        <div class='panel'>
            <div class='panel-heading'>
                <div class='panel-title'><?php echo $value; ?></div>
            </div>
            <?php foreach ($lang->secondmonthreport->menuName[$key] as $k => $v): ?>
                <?php
                list($name, $module, $method,$menuicon) = explode('|', $v);
                if(common::hasPriv($module, $method)) {
                ?>
            <div class='panel-body'>
                <div class='list-group'>
                    <?php

                        $class = $selected == $k ? 'selected' : '';
                        echo html::a($this->createLink($module, $method, ""), '<i class="icon ' . $menuicon . '"></i> ' . $name, '', "class='$class' title='$name'");

                    ?>
                </div>
            </div>
            <?php } endforeach;?>
        </div>
    </div>
<?php endforeach; ?>
<?php
//$this->app->loadLang('project');
//
//if(commonModel::hasPriv('secondmonthreport','customReport') && array_key_exists($this->app->user->account,$this->lang->secondmonthreport->monthReportCustomUser)){
//?>
<!--<div class="cell">-->
<!--    <div class='panel'>-->
<!---->
<!---->
<!--            <div class='panel-body'>-->
<!--                <div class='list-group text-center'>-->
<!---->
<!---->
<!--                    --><?php
//                        $customreportUrl = $this->createLink('secondmonthreport','customReport').'?onlybody=yes';
//                        echo html::commonButton($lang->secondmonthreport->customTimeInterval,"data-toggle='modal' data-type='iframe' data-url='{$customreportUrl}'","btn btn-primary");
//                    ?>
<!---->
<!--                </div>-->
<!--            </div>-->
<!---->
<!--    </div>-->
<!--</div>-->
<!--    --><?php
//
//}
//?>
