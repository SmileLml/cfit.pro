<?php
/**
 * Created by Yanqi Tong
 */
?>
<?php include '../../../common/view/header.html.php';?>
<div id='mainMenu' class='clearfix'>
  <div class='btn-toolbar pull-left'>
      <span class='btn btn-link btn-text'><a href="project-manageProducts-<?php echo $projectIDnow;?>.html"><span class='text'><?php echo $lang->project->manageProducts;?></span></a></span>
      <span class='btn btn-link btn-active-text'><span class='text'><?php echo $lang->project->manageProductPlans; ?></span></span>
  </div>
</div>
<div id='mainContent'>
  <div class='cell'>
    <form class='main-form form-ajax' method='post' id='productsBox' enctype='multipart/form-data'>
      <div class='detail'>
        <div class='detail-title'><?php echo $lang->project->linkedProducts;?>版本 <a href="projectplan-relationExec-<?php echo $projectIDnow;?>.html?onlybody=yes" class="btn iframe edit-icon" title="编辑关联" data-app="platform"><i class="icon-projectplan-exec icon-edit"></i></a></div>
        <div class='detail-content row content-box' >
          <?php $i = 0; foreach($allProducts as $productID => $productName):?>

          <?php $i++; $isDisabled = "disabled='disabled'";?>
          <?php $title      = $productName;?>
          <?php $checked    = 'checked';?>
          <div class='col-sm-4'>
            <div class='product <?php echo $checked;?>'>
              <div class="checkbox-primary" title='<?php echo $title;?>'>
                <?php echo "<input type='checkbox' name='products[$productID]' value='$productID' $checked id='products{$productID}' $isDisabled>";?>
                <label class='text-ellipsis checkbox-inline' for='<?php echo 'products' . $productID;?>' title='<?php echo $productName;?>'><?php echo $productName;?></label>
              </div>
             </div>
          </div>

          <?php unset($allProducts[$productID]);?>

          <?php endforeach;?>
        </div>
            <?php if(empty($i)){ ?>
            <div class='detail-content row' style="text-align: center; color: #8a8a8a"> 暂无关联产品计划 </div>

            <?php }?>
      </div>

      <div class="detail text-center form-actions">
      </div>
    </form>
  </div>
</div>
<style>
   .content-box {padding-top: 5px;}
   .edit-icon {float: right; margin-top:-7px }
</style>
<?php include '../../../common/view/footer.html.php';?>
