<?php if($component->type=='public'): ?>
    <?php include './reviewpublic.html.php'; ?>
<?php else:?>
    <?php include './reviewother.html.php'; ?>
<?php endif;?>