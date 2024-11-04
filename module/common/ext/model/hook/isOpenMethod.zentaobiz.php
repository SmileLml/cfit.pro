<?php
if($module == 'file' and $method == 'ajaxwopifiles') return true;
if($module == 'doclib' and $method == 'ajaxwopifiles') return true;
if($module == 'projectdoc' and $method == 'ajaxwopifiles') return true;
if($module == 'my' and $method == 'savepublish') return true;
if($module == 'my' and $method == 'publish') return true;
if($module == 'review' and $method == 'editfiles') return true;