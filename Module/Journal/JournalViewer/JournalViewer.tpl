<?php
use \AioSystem\Module\Journal\ClassJournalViewer as JournalViewer;
?>
<div id="JournalViewerJournalContent" class="ui-tabs" style="width: 70%;overflow:hidden;position: absolute;top:0;right:0;">
	<ul>
		<li><a href="#JournalViewerJournalAllContent"><span>All</span></a></li>
		<li><a href="#JournalViewerJournalErrorContent"><span>Error</span></a></li>
		<li><a href="#JournalViewerJournalExceptionContent"><span>Exception</span></a></li>
		<li><a href="#JournalViewerJournalShutdownContent"><span>Shutdown</span></a></li>
		<li><a href="#JournalViewerJournalDebugContent"><span>Debug</span></a></li>
	</ul>
	<div id="JournalViewerJournalAllContent" class="ui-tabs-hide">
		<?php print JournalViewer::GetJournalContent( JournalViewer::CONTENT_ALL, 80 );?>
	</div>
	<div id="JournalViewerJournalErrorContent" class="ui-tabs-hide">
		<?php print JournalViewer::GetJournalContent( JournalViewer::CONTENT_ERROR, 30 );?>
	</div>
	<div id="JournalViewerJournalExceptionContent" class="ui-tabs-hide">
		<?php print JournalViewer::GetJournalContent( JournalViewer::CONTENT_EXCEPTION, 20 );?>
	</div>
	<div id="JournalViewerJournalShutdownContent" class="ui-tabs-hide">
		<?php print JournalViewer::GetJournalContent( JournalViewer::CONTENT_SHUTDOWN, 20 );?>
	</div>
	<div id="JournalViewerJournalDebugContent" class="ui-tabs-hide">
		<?php print JournalViewer::GetJournalContent( JournalViewer::CONTENT_DEBUG, 50 );?>
	</div>
</div>
<script type="text/javascript">
	jQuery('div#JournalViewerJournalContent').tabs({collapsible:true,selected:-1}).draggable({handle:'ul',cursor:'pointer'}).resizable();
</script>