<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php if(!isonlybody()):?>
    <?php echo html::a(inlink('browse', "project=$review->project"), '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-secondary'");?>
    <div class="divider"></div>
    <?php endif;?>
    <div class="page-title">
      <span class="label label-id"><?php echo $review->id?></span>
      <span class="text"><?php echo $review->title . $lang->arrow . zget($lang->review->objectList, $review->object);?></span>
    </div>
  </div>
</div>
<div id='mainContent' class='main-row'>
  <div class='main-col'>
  <form method='post' id="assessForm" <?php echo isonlybody() ? 'target="hiddenwin"' : 'class="form-ajax"';?>>
      <div class='cell'>
        <div class='detail'>
          <div class='detail-title'><?php echo $lang->review->object;?></div>
          <div class='detail-content article-content no-margin no-padding'>
          <?php
          foreach($review->objects as $object)
          {
              echo '<div class="object-item"><a href="' . $object->url . '">' . $lang->review->objectList[$object->object] . '</a></div>';
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
      <div class='cell review-footer main-form'>
        <table class='table table-form'>
          <tr>
            <th class='w-80px'><?php echo $lang->review->result;?></th>
            <td><?php echo html::radio('result', $lang->review->resultList, isset($result->result) ? $result->result : 'pass');?></td>
          </tr>
          <tr>
            <th><?php echo $lang->review->method;?></th>
            <td><?php echo html::select('method', $lang->review->methodList, '', 'class="form-control"');?></td>
            <td>
              <div class='input-group'>
                <div class='input-group'>
                  <span class='input-group-addon'><?php echo $lang->review->reviewedDate;?></span>
                  <?php echo html::input('createdDate', helper::today(), 'class="form-control form-date"');?>
                  <span class='input-group-addon'><?php echo $lang->review->consumed;?></span>
                  <?php echo html::input('consumed', isset($result->consumed) ? $result->consumed : 0, "class='form-control'");?>
                  <span class='input-group-addon'>h</span>
                </div>
              </div>
            </td>
          </tr>
          <tr>
            <th><?php echo $lang->review->finalOpinion;?></th>
            <td colspan='2'>
            <?php echo html::textarea('opinion', isset($result->opinion) ? htmlspecialchars($result->opinion) : '', "rows='10' class='form-control'");?>
            </td>
          </tr>
          <tr>
            <td colspan='3' class='text-center form-actions'>
            <?php echo html::submitButton();?>
            <?php echo html::backButton();?>
            </td>
          </tr>
        </table>
      </div>
    </form>
  </div>
</div>
<?php js::set('stopSubmit', $lang->review->stopSubmit);?>
<?php include '../../common/view/footer.html.php';?>
