<?php
defined('_JEXEC') or die('Restricted access');

//Ordering allowed ?
$ordering = ($this->lists['order'] == 'jte.id');

JHTML::_('behavior.tooltip');
?>

<form action="index.php?option=com_joaktree" method="post" id="adminForm" name="adminForm">
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
			<th width="20">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->items ); ?>);" />
			</th>			
			<th>
				<?php echo JHTML::_('grid.sort', 'JTAPPS_HEADING_TITLE', 'japp.title', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>
			<th>
				<?php echo JHtml::_('grid.sort', 'JTAPPS_HEADING_DESCRIPTION', 'japp.description', $this->lists['order_Dir'], $this->lists['order']); ?>
			</th>
			<th>
				<?php echo JHtml::_('grid.sort', 'JTAPPS_HEADING_PROGRAM', 'japp.programName', $this->lists['order_Dir'], $this->lists['order']); ?>
			</th>
			<th>
				<?php echo JHtml::_('grid.sort', 'JTAPPS_HEADING_PERSONS', 'NumberOfPersons ', $this->lists['order_Dir'], $this->lists['order']); ?>
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
		$checked 	= JHTML::_('grid.id',   $i, $row->id );
		$click   	= 'return listItemTask(\'cb'.$i.'\', \'edit\')';				
	?>
		<tr class="<?php echo "row$k"; ?>">
			<td><?php echo $this->pagination->getRowOffset( $i ); ?></td>
			<td><?php echo $checked; ?></td>
			<td>
				<?php if ($this->canDo->get('core.edit')) : ?>
					<a href="javascript:void(0);" onclick="<?php echo $click; ?>" title="<?php echo JText::_( 'JTTHEMES_TOOLTIP_EDIT' ); ?>">
						<?php echo $this->escape($row->title); ?>
					</a>
				<?php else : ?>
						<?php echo $this->escape($row->title); ?>
				<?php endif; ?>
			</td>
			<td><?php echo $this->escape($row->description);?></td>
			<td><?php echo $this->escape($row->programName);?></td>
			<td><?php echo $this->escape($row->NumberOfPersons);?></td>
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
<input type="hidden" name="controller" value="jt_applications" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="" />

</form>