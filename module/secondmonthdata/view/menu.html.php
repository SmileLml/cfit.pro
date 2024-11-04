<div style="text-align:center">
    <ul class="nav nav-simple clearfix " style="background-color:#FFFFFF;display:inline-block;">
        <?php  foreach ($this->lang->secondmonthdata->topMenuTitle as $key => $value):
            $topmenuInfo = explode('|',$value);
            if(common::hasPriv($topmenuInfo[1], $topmenuInfo[2])){
                ?>
                <li class="<?php if($key == $topmenukey){echo 'active';}?>">
                    <a href="<?php echo helper::createLink($topmenuInfo[1],$topmenuInfo[2]);?>"><?php echo $topmenuInfo[0]; ?></a>
                </li>

            <?php } endforeach; ?>
    </ul>
</div>