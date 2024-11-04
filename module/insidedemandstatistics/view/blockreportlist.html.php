<div class="sidebar-toggle">
  <i class="icon icon-angle-left"></i>
</div>
<div class="cell">
  <div class='panel'>
    <div class='panel-heading'>
      <div class='panel-title'>统计分类</div>
    </div>
    <div class='panel-body'>
      <div class='list-group'>
        <?php
        $class = $selected == 1 ? 'selected' : '';
        echo html::a($this->createLink("insidedemandstatistics", "opinion", ""), '<i class="icon icon-file-text"></i> ' . '需求意向-需求来源类型统计表', '', "class='$class' title='需求意向-需求来源类型统计表'");
        $class = $selected == 2 ? 'selected' : '';
        echo html::a($this->createLink("insidedemandstatistics", "opinion2", ""), '<i class="icon icon-file-text"></i> ' . '需求意向-需求种类统计表', '', "class='$class' title='需求意向-需求种类统计表'");
        $class = $selected == 3 ? 'selected' : '';
        echo html::a($this->createLink("insidedemandstatistics", "requirement", ""), '<i class="icon icon-file-text"></i> ' . '需求任务统计表', '', "class='$class' title='需求任务统计表'");
        $class = $selected == 4 ? 'selected' : '';
        echo html::a($this->createLink("insidedemandstatistics", "demand", ""), '<i class="icon icon-file-text"></i> ' . '需求条目-实施单位统计表', '', "class='$class' title='需求条目-实施单位统计表'");
        ?>
      </div>
    </div>
  </div>

</div>
