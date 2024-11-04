<style>
#book .tree li.active>span.item{font-weight: 700; color: #0c64eb;}
#book .tree li.active>span.item a{font-weight: 700; color: #0c64eb;}
</style>
<?php $activeClass = (strpos(",browse,managebook,catalog,edit", ",{$this->methodName},") !== 'false' && isset($currentLib->id) && $currentLib->id == $libID) ? 'active' : '';?>
  <?php $serials  = $this->doc->computeSN($libID); ?>
  <?php $nodeList = $this->doc->getBookStructure($libID);?>
  <ul data-name="docsTree" data-ride="tree" data-initial-state="preserve" class="tree no-margin">
    <?php foreach($nodeList as $nodeInfo):?>
    <?php $serial = $nodeInfo->type != 'book' ? $serials[$nodeInfo->id] : '';?>
    <?php if($nodeInfo->parent != 0) continue;?>
    <?php $activeClass = (isset($doc->id) && $doc->id == $nodeInfo->id) ? 'active' : '';?>
      <li <?php echo "class='open $activeClass'";?>>
      <?php if($nodeInfo->type == 'chapter'):?>
      <?php echo "<a>{$nodeInfo->title}</a>";?>
      <?php elseif($nodeInfo->type == 'article'):?>
      <?php echo "<span class='item'>{$serial} " . html::a(helper::createLink('doc', 'objectLibs', "type=book&objectID=0&libID=$libID&docID={$nodeInfo->id}"), $nodeInfo->title, '') . '</span>';?>
      <?php endif;?>
      <?php if(!empty($nodeInfo->children)) $this->doc->getFrontCatalog($nodeInfo->children, $serials, isset($doc->id) ? $doc->id : 0);?>
      </li>
    <?php endforeach;?>
  </ul>
