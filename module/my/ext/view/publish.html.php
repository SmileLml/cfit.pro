<?php include '../../../common/view/header.lite.html.php';?>
<?php include '../../../common/view/carousel.html.php';?>

<main id='features' class="enabled">
  <header>
    <ul class='nav nav-simple' id='featuresNav'>
    <?php $i=0; $j=0; $x=0;?>
    <?php foreach($result as $publish):?>
        <li <?php echo $i == 0 ? "class='active'" : '';?>><a class='slide-feature-to text-ellipsis' style="width:400px" title="<?php echo "【".zget(json_decode($typeValue['type']),$publish->type,'').'】'.$publish->name?>" data-slide-to='<?php echo $i ?>' href='#featuresCarousel'><?php echo "【".zget(json_decode($typeValue['type']),$publish->type,'').'】'.$publish->name?></a></li>
    <?php $i++;?>
    <?php endforeach;?>
    </ul>
  </header>

  <div id='featuresCarousel' class='carousel slide' data-ride='carousel' data-interval='false' style='overflow: auto;'>
    <ol class='carousel-indicators'>
    <?php foreach($result as $publish):?>
            <li data-target='#featuresCarousel' data-slide-to='<?php echo $j ?>' <?php echo $j == 0 ? "class='active'" : ''?>></li>
    <?php $j++;?>
    <?php endforeach;?>
    </ol>

    <div class='carousel-inner'>
    <?php foreach($result as $publish):?>
      <div class='item  <?php echo $x == 0 ? 'active' : '';?>'  >
        <div class='detaile-content article-content' >
        <?php echo !empty($publish->content) ? $publish->content : "<div class='text-center text-muted'>" . $publish->content . '</div>';?>
        </div>
      </div>
      <?php $x++;?>
      <?php endforeach;?>
    </div>
  </div>
  <footer>
    <button type='button' class='btn btn-primary btn-wide slide-feature-to-prev btn-slide-prev'>上一个 </button> 
    <button type='button' class='btn btn-primary btn-wide slide-feature-to-next btn-slide-next'>下一个 </button>
    <button type='button' data-dismiss='modal' class='btn btn-primary btn-wide btn-close-modal' id="closePublish">关闭</button>
  </footer>
</main>
<?php include '../../../common/view/footer.lite.html.php';?>
