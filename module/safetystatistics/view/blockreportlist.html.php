<div class="sidebar-toggle">
    <i class="icon icon-angle-left"></i>
</div>
<?php foreach ($this->lang->safetystatisstics->menuTitle as $key => $value): ?>
    <div class="cell">
        <div class='panel'>
            <div class='panel-heading'>
                <div class='panel-title'><?php echo $value; ?></div>
            </div>
            <?php foreach ($lang->safetystatisstics->menuName[$key] as $k => $v): ?>
            <div class='panel-body'>
                <div class='list-group'>
                    <?php
                    list($name, $module, $method) = explode('|', $v);
                    $class = $selected == $k ? 'selected' : '';
                    echo html::a($this->createLink($module, $method, ""), '<i class="icon icon-file-text"></i> ' . $name, '', "class='$class' title='$name'");
                    ?>
                </div>
            </div>
            <?php endforeach;?>
        </div>
    </div>
<?php endforeach; ?>
