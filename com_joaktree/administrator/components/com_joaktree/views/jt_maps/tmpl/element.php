<?php
defined('_JEXEC') or die('Restricted access');

//Ordering allowed ?
$ordering = ($this->lists['order'] == 'jte.id');

JHTML::_('behavior.tooltip');
?>

<form action="index.php?option=com_joaktree&amp;view=jt_maps&amp;layout=element&amp;task=element&amp;tmpl=component"                method="post" id="adminForm" name="adminForm">
<?php echo JHTML::_( 'form.token' ); ?>

	<table class="adminlist">
		<tr>
			<td align="left" width="100%"><?php echo JText::_( 'JT_LABEL_FILTER' ); ?>:
				<input type="text" name="search" id="search" value="<?php echo $this->lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
				<button onclick="this.form.submit();"><?php echo JText::_( 'JT_LABEL_GO' ); ?></button>
				<button onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'JT_LABEL_RESET' ); ?></button>
			</td>
		</tr>
	</table>

<div id="editcell">
	<table class="adminlist">
	<thead>
		<tr>
			<th width="5"><?php echo JText::_( 'JT_HEADING_NUMBER' ); ?></th>
			<th>
				<?php echo JHTML::_('grid.sort', 'JTAPPS_LABEL_TITLE', 'jmp.name', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>
			<th><?php echo JText::_( 'JT_LABEL_TYPE' ); ?></th>
			<th><?php echo JText::_( 'JT_LABEL_SUBJECT' ); ?></th>
			<th>
				<?php echo JHtml::_('grid.sort', 'JT_LABEL_PERIODSTART', 'jmp.period_start', $this->lists['order_Dir'], $this->lists['order']); ?>
			</th>
			<th>
				<?php echo JHtml::_('grid.sort', 'JT_LABEL_PERIODEND', 'jmp.period_end', $this->lists['order_Dir'], $this->lists['order']); ?>
			</th>
			<th width="2%">
				<?php echo JText::_( 'JT_HEADING_ID' ); ?>
			</th>
		</tr>
	</thead>

	<tfoot>
		<tr>
			<td colspan="7"><?php echo $this->pagination->getListFooter(); ?></td>
		</tr>
	</tfoot>
	
	<tbody>
	<?php
	$k = 0;
	for ($i=0, $n=count( $this->items ); $i < $n; $i++)	{
		$row = &$this->items[$i];
		$click   	= 'return listItemTask(\'cb'.$i.'\', \'edit\')';				
		$linkname  = str_replace("'", "\&#39;", $row->name);
		$appTitle  = str_replace("'", "\&#39;", $row->appTitle);
		$link 	= 'window.parent.jSelectMap(\''.$row->id.'\', \''.$linkname.'\', \''.$row->app_id.'\', \''.$appTitle.'\' );'; 
		$anker  = 'style="cursor: pointer;" onclick="'.$link.'"';
	?>
		<tr class="<?php echo "row$k"; ?>">
			<td><?php echo $this->pagination->getRowOffset( $i ); ?></td>
			<td><a <?php echo $anker ?>><?php echo $this->escape($row->name); ?></a></td>
			<td><a <?php echo $anker ?>><?php echo $this->escape($row->selection);?>&nbsp;/&nbsp;<?php echo $this->escape($row->service);?></a></td>
			<td><?php echo $this->escape($row->subject);?></td>
			<td><?php echo $this->escape($row->period_start);?></td>
			<td><?php echo $this->escape($row->period_end);?></td>
			<td class="center"><?php echo $row->id; ?></td>
		</tr>
		<?php
		$k = 1 - $k;
	}
	?>
        </tbody>

	</table>
</div>


<input type="hidden" name="option" value="com_joaktree" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="jt_maps" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="" />

</form>