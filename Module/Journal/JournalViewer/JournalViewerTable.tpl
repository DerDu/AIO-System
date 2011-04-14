<style type="text/css">
	table#JournalViewerTableType{JournalType} * {
		color: #000000;
	}
</style>
<table id="JournalViewerTableType{JournalType}">
	<thead>
		<tr>
			<th>Datetime</th>
			<th>Session</th>
			<th>Entry</th>
		</tr>
	</thead>
	<tbody>
	{JournalEntryList}
		<tr>
			<td style="{CategoryStyle}">
				<span style="display:none;">{EntrySort}</span>
				{EntryTimestamp}
				{CategoryType}
			</td>
			<td style="text-align:center;">
				{SessionId}<br/>{SessionUser}
			</td>
			<td style="{ContentStyle}">
				<span style="font-size:0.9em;">{Content}</span>
			</td>
		</tr>
	{/JournalEntryList}
	</tbody>
	<tfoot>
		<tr>
			<td colspan="3">
				Files: {FileCount}
				Entries: {EntryCount}
			</td>
		</tr>
	</tfoot>
</table>
<script type="text/javascript">
	var oTable = jQuery('table#JournalViewerTableType{JournalType}').dataTable({
		aaSorting: [[ 0, "desc" ]],
		bAutoWidth: false,
		bLengthChange: false,
		iDisplayLength : {JournalLength},
		bPaginate: true
	});
	oTable.parent('div.dataTables_wrapper')
		.find('div.dataTables_filter').css({width:'99.9%','margin-bottom':'1px'})
		.find('input').css({width:'100%'})
	.parents('div.dataTables_wrapper')
		.find('div.dataTables_length').css({float:'right'});
</script>