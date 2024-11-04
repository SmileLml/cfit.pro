<?php include '../../../common/view/header.html.php';?>
<div id="mainContent" class="main-content">
    <div class="main-col col-8">
        <div class='cell'>
            <div class='detail'>
                <div class='detail-title'><?php echo '合并单元格';?></div>
                <div class='detail-content article-content no-margin no-padding'>
                    <table class="table ops  table-fixed ">
                        <thead>
                        <tr>
                            <th class='w-90px' ><?php echo '数据' ;?></th>
                            <th class='w-90px' ><?php echo '子阶段' ;?></th>
                            <th class='w-160px'><?php echo '姓名' ;?></th>
                            <th class='w-100px'><?php  echo '年龄'  ;?></th>
                        </tr>
                        </thead>

                        <?php
                            foreach($dataList as $key => $val):
                                $count = count($val);
                                $dataCount = helper::getArrayLeafDataCount($val);
                                //第一数组
                                $firstVal = $val[0];

                                $dataCount1 = count($firstVal);
                                //第一个子数组
                                $firstSubVal = $firstVal[0];

                            ?>
                            <tr>
                                <td rowspan="<?php echo $dataCount ;?>"><?php echo $dataCount ;?></td>
                                <td  rowspan="<?php echo $dataCount1 ;?>"><?php echo $dataCount1 ;?></td>
                                <td><?php echo $firstSubVal->name ;?></td>
                                <td><?php echo $firstSubVal->age ;?></td>
                            </tr>
                        <?php
                            if($dataCount1 > 1):
                                unset($firstVal[0]);
                            foreach ($firstVal as $val2):
                        ?>
                                <tr>
                                    <td><?php echo $val2->name ;?></td>
                                    <td><?php echo $val2->age ;?></td>
                                </tr>

                        <?php
                                endforeach;
                            endif;
                        ?>



                        <?php
                        if($count > 1):
                            unset($val[0]);
                            foreach ($val  as $key1 => $val1):
                                $dataCount1 = helper::getArrayLeafDataCount($val1);
                                $firstInfo = $val1[0];
                        ?>
                                <tr>
                                    <td rowspan="<?php echo $dataCount1 ;?>"><?php echo $dataCount1 ;?></td>
                                    <td><?php echo $firstInfo->name ;?></td>
                                    <td><?php echo $firstInfo->age ;?></td>
                                </tr>
                                <?php
                                if($dataCount1 > 1):
                                    unset($val1[0]);
                                    foreach ($val1 as $val2):
                                ?>
                                    <tr>
                                        <td><?php echo $val2->name ;?></td>
                                        <td><?php echo $val2->age ;?></td>
                                    </tr>
                                <?php
                                    endforeach;
                                endif;
                                ?>


                        <?php
                                endforeach;
                             endif;
                        ?>

                        <?php
                            endforeach;
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include '../../../common/view/footer.html.php';?>
