<?php js::set('productID', $productID);?>
<?php
$iCharges = 0;
$others   = 0;
$closeds  = 0;
$openApp  = 'project';
$productNames = array();

foreach($products as $product)
{
    if($product->status == 'normal' and $product->PO == $this->app->user->account) $iCharges++;
    if($product->status == 'normal' and !($product->PO == $this->app->user->account)) $others++;
    if($product->status == 'closed') $closeds++;
    $productNames[] = $product->name;
}
$productsPinYin     = common::convert2Pinyin($productNames);
$myProductsHtml     = '';
$normalProductsHtml = '';
$closedProductsHtml = '';

foreach($products as $product)
{
    $selected    = $product->id == $productID ? 'selected' : '';
    $productName = $product->name;
    $linkHtml    = sprintf($link, $product->id);
    if($product->status == 'normal' and $product->PO == $this->app->user->account)
    {
        $myProductsHtml .= html::a($linkHtml, $productName, '', "class='text-important $selected' title='{$productName}' data-key='" . zget($productsPinYin, $product->name, '') . "' data-app='$openApp'");
    }
    else if($product->status == 'normal' and !($product->PO == $this->app->user->account))
    {
        $normalProductsHtml .= html::a($linkHtml, $productName, '', "class='$selected' title='{$productName}' data-key='" . zget($productsPinYin, $product->name, '') . "' data-app='$openApp'");
    }
    else if($product->status == 'closed')
    {
        $closedProductsHtml .= html::a($linkHtml, $productName, '', "class='$selected' title='{$productName}' class='closed' data-key='" . zget($productsPinYin, $product->name, '') . "' data-app='$openApp'");
    }
}
?>
<div class="table-row">
  <div class="table-col col-left">
    <div class='list-group'>
      <?php
      if(!empty($myProductsHtml))
      {
          echo "<div class='heading'>{$lang->product->mine}</div>";
          echo $myProductsHtml;
          if(!empty($myProductsHtml))
          {
              echo "<div class='heading'>{$lang->product->other}</div>";
          }
      }
      echo $normalProductsHtml;
      ?>
    </div>
    <div class="col-footer">
      <a class='pull-right toggle-right-col not-list-item'><?php echo $lang->product->closed?><i class='icon icon-angle-right'></i></a>
    </div>
  </div>
  <div class="table-col col-right">
   <div class='list-group'><?php echo $closedProductsHtml;?></div>
  </div>
</div>
<script>scrollToSelected();</script>
