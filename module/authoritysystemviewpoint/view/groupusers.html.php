<?php include '../../common/view/header.html.php';?>
<div id='mainContent' class='main-content'>
    <div class='center-block'>
        <div class='main-header'>
            <h2>
                <span class='label label-id'><?php echo $list->id;?></span>
                <?php echo  ("<span title='$list->id'>" . $lang->authoritysystemviewpoint->groupUsers . '</span>') ;?>
            </h2>
        </div>
        <div id="" class="main-row">
                <div class="detail">
                    <div class="detail-content article-content">
                        <table class ='table table-fixed'>
                            <thead>
                              <th><?php echo  $lang->authoritysystemviewpoint->userList;?></th>
                            </thead>
                            <tbody>
                              <td style="white-space:normal"><?php echo  str_replace(',',' 、 ',$list->users);?></td>
                            </tbody>
                        </table>
                    </div>
                </div>
        </div>
    </div>
</div>
<?php include '../../common/view/footer.html.php';?>
