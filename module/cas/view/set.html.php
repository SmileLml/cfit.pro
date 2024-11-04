<?php include $app->getModuleRoot() .'common/view/header.html.php';?>
<div id='mainContent' class='main-content'>
  <div class='center-block'>
    <div class='main-header'>
      <h2><?php echo $lang->cas->common?></h2>
    </div>
    <form class='main-form' method='post' target='hiddenwin'>
        <div class='detail-title'><?php echo $lang->cas->base?></div>
        <table class='table table-form'>
          <tr>
            <th style="width: 120px"><?php echo $lang->cas->turnon?></th>
            <td class='w-400px'><?php echo html::select('turnon', $lang->cas->turnonList, empty($casConfig->turnon) ? '' : $casConfig->turnon, "class='form-control'")?></td>
            <td></td>
          </tr>
          <tr>
            <th><?php echo $lang->cas->loginUrl?></th>
            <td><?php echo html::input('loginUrl', empty($casConfig->loginUrl) ? '' : $casConfig->loginUrl, "class='form-control' autocomplete='off'")?></td>
            <td><?php echo $lang->cas->example . 'http://cas.test.com/cas/login'?></td>
          </tr>
          <tr>
            <th><?php echo $lang->cas->loginOut?></th>
            <td><?php echo html::input('loginOut', empty($casConfig->loginOut) ? '' : $casConfig->loginOut, "class='form-control' autocomplete='off'")?></td>
            <td><?php echo $lang->cas->example . 'http://cas.test.com/cas/logout'?></td>
          </tr>
          <tr>
            <th><?php echo $lang->cas->authUrl?></th>
            <td><?php echo html::input('authUrl', empty($casConfig->authUrl) ? '' : $casConfig->authUrl, "class='form-control' autocomplete='off'")?></td>
            <td><?php echo $lang->cas->example . 'http://cas.test.com/cas/serviceValidate'?></td>
          </tr>
          <tr>
            <th><?php echo $lang->cas->serviceUrl?></th>
            <td><?php echo html::input('serviceUrl', empty($casConfig->serviceUrl) ? '' : $casConfig->serviceUrl, "class='form-control' autocomplete='off'")?></td>
            <td><?php echo $lang->cas->example . 'http://test.com/cas-tokenlogin.html'?></td>
          </tr>
          <tr>
                <th><?php echo $lang->cas->mobileServiceUrl?></th>
                <td><?php echo html::input('mobileServiceUrl', empty($casConfig->mobileServiceUrl) ? '' : $casConfig->mobileServiceUrl, "class='form-control' autocomplete='off'")?></td>
                <td><?php echo $lang->cas->example . 'http://test.com/cas-tokenlogin.html'?></td>
         </tr>
          <tr>
            <td colspan='3' class='text-center form-actions'>
              <?php echo html::submitButton() . ' '. html::backButton();?></td>
            </td>
          </tr>
        </table>
    </form>
  </div>
</div>
<?php include $app->getModuleRoot() .'common/view/footer.html.php';?>

