<?php
$namePinyinList = array();
foreach($applications as $name) $namePinyinList[] = $name;
$namePinyinList = common::convert2Pinyin($namePinyinList);

$normalProductsHtml = '';
foreach($applications as $id => $applicationName)
{
    $selected = $id == $applicationID ? 'selected' : '';
    $linkHtml = $this->rebirth->setParamsForLink($module, $link, $id);
    $normalProductsHtml .= html::a($linkHtml, $applicationName, '', "class='$selected' title='{$applicationName}' data-key='" . zget($namePinyinList, $applicationName, '') . "' data-app='$openApp'");
}
?>
<div class="table-row">
  <div class="table-col col-left">
    <div class='list-group'>
      <?php
      echo $normalProductsHtml;
      ?>
    </div>
  </div>
</div>
<script>scrollToSelected();</script>
