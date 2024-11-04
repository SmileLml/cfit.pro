<?php include '../../../common/view/header.html.php';?>
<?php include '../../../common/view/kindeditor.html.php';?>
<div id='mainContent' class='main-content fade in  scrollbar-hover' style="min-height:300px; max-height: 500px;">
  <div class='center-block'>
    <div class='main-header'>
      <h2>
        <span class='label label-id'><?php echo $review->id;?></span>
        <span><?php echo $review->title;?></span>

        <small><?php echo $lang->arrow . $lang->review->submit;?></small>
      </h2>
    </div>
      <?php if(!$checkRes['result']):?>
          <div class="tipMsg">
              <span><?php echo $checkRes['message']; ?></span>
          </div>
      <?php else:?>
          <?php include $view;?>
          <!--
            <hr class='small' />
            <div class='main'><?php include '../../../common/view/action.html.php';?></div>
            -->
      <?php endif;?>
  </div>
</div>
<?php include '../../../common/view/footer.html.php';?>
