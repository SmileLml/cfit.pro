<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<style>
    .reviewTip{color: red;}
</style>
<div id="mainContent" class="main-content fade" style="height:380px;">
  <div class="center-block">

          <div class='main-header'>
              <h2>
                  <span class='label label-id'><?php echo $change->code;?></span>
                  <small><?php echo  $lang->change->appoint;?></small>
              </h2>
          </div>


              <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
              <table class="table table-form">
                <tbody>
                <tr>
                    <th><?php echo $lang->change->appoint;?></th>
                    <!--                      picker-select-->
                    <td><?php echo html::select('pointusers[]', $users, '', "class='form-control chosen' multiple");?></td>
                </tr>
                <tr>
                    <th></th>
                    <!--                      picker-select-->
                    <td></td>
                </tr>
                <tr >
                    <?php if($appointUser){
                       ?>

                        <th><?php echo $lang->change->appointAlreadyUser;?>:</th>
                        <td>
                            <?php foreach ($appointUser as $puser){
                                echo zget($users,$puser).',';
                            }
                            ?>

                        </td>
        <?php
                    }else{
                        ?>
                        <th></th>
                        <td>


                        </td>
                        <?php
                    }?>

                </tr>
                <tr>
                    <th></th>
                    <!--                      picker-select-->
                    <td></td>
                </tr>


                  <tr>
                    <td class='form-actions text-center' colspan='3'>

                        <?php echo html::submitButton() . html::backButton();?>
                    </td>
                  </tr>
                </tbody>
              </table>
            </form>

  </div>
</div>




<?php include '../../common/view/footer.html.php';?>