<?php
if ($app->moduleName == 'my') $app->openApp = 'my';
if($extView = $this->getExtViewFile(__FILE__)){include $extView; return helper::cd();}
include 'header.lite.html.php';
include 'chosen.html.php';
//include 'validation.html.php';
?>
<style>
.red-point {position: relative;}
.red-point::before {content: " "; border: 3px solid red; border-radius:3px; position: absolute; z-index: 1000; right: 0%; margin-right: -8px; margin-top: -0px; border: 3px solid red;}
.red-pending {color: red;}
</style>
<?php if(empty($_GET['onlybody']) or $_GET['onlybody'] != 'yes'):?>
<?php $this->app->loadConfig('sso');?>
<?php if(!empty($config->sso->redirect)) js::set('ssoRedirect', $config->sso->redirect);?>
<header id='header'>
  <div id='mainHeader'>
    <div class='container'>
      <div id='heading'>
        <?php common::printHomeButton($app->openApp);?>
        <?php echo isset($lang->switcherMenu) ? $lang->switcherMenu : '';?>
      </div>
      <nav id='navbar'><?php $activeMenu = commonModel::printMainMenu();?></nav>
      <div id='toolbar'>
        <div id='userMenu'>
          <ul id="userNav" class="nav nav-default">
            <li class='dropdown dropdown-hover has-avatar'><?php common::printUserBar();?></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <?php if(isset($lang->{$app->openApp}->menu->$activeMenu) and isset($lang->{$app->openApp}->menu->{$activeMenu}['subMenu'])):?>
  <div id='subHeader'>
    <div class='container'>
      <div id="pageNav" class='btn-toolbar'><?php if(isset($lang->modulePageNav)) echo $lang->modulePageNav;?></div>
        <!-- begin 导航添加操作图标-->
        <?php if(isset($extraIcons)){?>
        <nav id='subNavbar' style="float:left "><span style="margin-left: 700px;"><?php common::printModuleMenu($activeMenu);?></span></nav>
        <div style=" margin-top:15px;float: right"><?php echo $extraIcons;?></div>
        <!-- end 导航添加操作图标-->
        <?php } else { ?>
        <nav id='subNavbar' ><?php common::printModuleMenu($activeMenu);?></nav>
        <?php } ?>
      <div id="pageActions"><div class='btn-toolbar'><?php if(isset($lang->TRActions)) echo $lang->TRActions;?></div></div>

    </div>
  </div>
  <?php endif;?>
  <?php
  if(!empty($config->sso->redirect))
  {
      css::import($defaultTheme . 'bindranzhi.css');
      js::import($jsRoot . 'bindranzhi.js');
  }
  ?>
</header>

<?php endif;?>
<script>
adjustMenuWidth();
</script>

<main id='main' <?php if(!empty($config->sso->redirect)) echo "class='ranzhiFixedTfootAction'";?> >
  <div class='container'>
