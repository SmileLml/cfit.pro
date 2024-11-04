<?php
/**
 * The view file of bug module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     bug
 * @version     $Id: view.html.php 4728 2013-05-03 06:14:34Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php $browseLink = $this->session->reviewList ? $this->session->reviewList : inlink('browse', "project=$review->project");?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <div class="page-title">
      <span class="label label-id"><?php echo $review->id?></span>
      <span class="text"><?php echo $review->title;?></span>
    </div>
  </div>
</div>
<div id="mainContent" class="main-row">
  <div class="main-col col-8">
    <div class='cell'>
      <div class='detail'>
        <div class='detail-title'><?php echo $lang->review->object;?></div>
        <div class='detail-content article-content no-margin no-padding'>
        <?php
        foreach($review->objects as $object)
        {
            $previewLink = $this->fetch('projectdoc', 'filePreview', array($review->project, $object->url, $lang->review->objectList[$object->object]));
            echo '<div class="object-item">' . $previewLink . '</a></div>';
        }
        /*
        $url = $review->content;
        if(!preg_match('/^https?:\/\//', $review->content)) $url = 'http://' . $url;
        $urlIsHttps = strpos($url, 'https://') === 0;
        $serverIsHttps = ((isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == 'on') or (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) and strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) == 'https'));
        if(($urlIsHttps and $serverIsHttps) or (!$urlIsHttps and !$serverIsHttps))
        {   
            echo "<iframe width='100%' id='urlIframe' src='$url'></iframe>";
        }   
        else
        {   
            $parsedUrl = parse_url($url);
            $urlDomain = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
        
            $title    = ''; 
            $response = common::http($url);
            preg_match_all('/<title>(.*)<\/title>/Ui', $response, $out);
            if(isset($out[1][0])) $title = $out[1][0];
        
            echo "<div id='urlCard'>";
            echo "<div class='url-icon'><img src='{$urlDomain}/favicon.ico' width='45' height='45' /></div>";
            echo "<div class='url-content'>";
            echo "<div class='url-title'>{$title}</div>";
            echo "<div class='url-href'>" . html::a($url, $url, '_target') . "</div>";
            echo "</div></div>";
        }
         */
        ?> 
        </div>
      </div>
    </div>
    <div class='cell'><?php include '../../common/view/action.html.php';?></div>
    <div class='main-actions'>
      <div class="btn-toolbar">
        <?php $params = "reviewID=$review->id"; ?>
        <?php common::printBack($browseLink);?>
        <div class='divider'></div>
        <?php
        common::printIcon('review', 'assess', $params, $review, 'button', 'glasses');
        //common::printIcon('review', 'result', "project=$review->project&" . $params, $review, 'button', 'list-alt', '', 'iframe', true);
        common::printIcon('review', 'edit',   $params, $review);
        ?>
      </div>
    </div>
  </div>
  <div class="side-col col-4">
    <div class="cell">
      <div class='detail'>
        <div class='detail-title'><?php echo $lang->review->basicInfo;?></div>
        <div class='detail-content'>
          <table class='table table-data'>
            <tbody>
              <tr>
                <th class='w-100px'><?php echo $lang->review->status;?></th>
                <td><?php echo zget($lang->review->statusList, $review->status);?></td>
              </tr>
              <tr>
                <th><?php echo $lang->review->type;?></th>
                <td><?php echo zget($lang->review->typeList, $review->type, '');?></td>
              </tr>
              <tr>
                <th><?php echo $lang->review->owner;?></th>
                <td><?php $owners = explode(',', str_replace(' ', '', $review->owner)); foreach($owners as $account) echo ' ' . zget($users, $account);?></td>
              </tr>
              <tr>
                <th><?php echo $lang->review->expert;?></th>
                <td><?php $experts = explode(',', str_replace(' ', '', $review->expert)); foreach($experts as $account) echo ' ' . zget($users, $account);?></td>
              </tr>
              <tr>
                <th><?php echo $lang->review->reviewedBy;?></th>
                <td><?php $reviewer = explode(',', str_replace(' ', '', $review->reviewedBy)); foreach($reviewer as $account) echo ' ' . zget($users, $account);?></td>
              </tr>
              <tr>
                <th><?php echo $lang->review->outside;?></th>
                <td><?php $reviewer = explode(',', str_replace(' ', '', $review->outside)); foreach($reviewer as $account) echo ' ' . zget($outside, $account);?></td>
              </tr>
              <tr>
                <th><?php echo $lang->review->deadline;?></th>
                <td><?php echo $review->deadline;?></td>
              </tr>
              <tr>
                <th><?php echo $lang->review->createdBy;?></th>
                <td><?php echo zget($users, $review->createdBy);?></td>
              </tr>
              <tr>
                <th><?php echo $lang->review->createdDate;?></th>
                <td><?php echo $review->createdDate;?></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
