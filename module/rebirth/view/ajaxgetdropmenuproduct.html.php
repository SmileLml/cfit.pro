<?php
$namePinyinList = array();
foreach($products as $name) $namePinyinList[] = $name;
$namePinyinList = common::convert2Pinyin($namePinyinList);

$normalProductsHtml = '';
foreach($products as $id => $productName)
{
    $selected = $id == $productID ? 'selected' : '';
    $linkHtml = $this->rebirth->setProductParamsForLink($module, $link, $applicationID, $id);
    $normalProductsHtml .= html::a($linkHtml, $productName, '', "class='$selected' title='{$productName}' data-key='" . zget($namePinyinList, $productName, '') . "' data-app='$openApp'");
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
