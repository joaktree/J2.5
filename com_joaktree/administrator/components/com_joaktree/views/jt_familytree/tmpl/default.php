<?php
defined('_JEXEC') or die('Restricted access');

//Ordering allowed ?
$ordering = ($this->lists['order'] == 'jte.id');

JHTML::_('behavior.tooltip');
?>
<script type="text/javascript">
	<?php echo $this->lists['jsscript']; ?>
</script>
<?php if ($this->lists['action'] == 'assign') { ?>
	<script>
		window.addEvent('domready', function() {
			assignFTInit(<?php echo $this->lists['act_treeId']; ?>);
		});
	</script>
<?php } ?>

<form action="index.php?option=com_joaktree" method="post" id="adminForm" name="adminForm">
<?php echo JHTML::_( 'form.token' ); ?>

	<table class="adminlist">
		<tr>
			<td align="left" width="100%"><?php echo JText::_( 'JT_LABEL_FILTER' ); ?>:
				<input type="text" name="search" id="search" value="<?php echo $this->lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
				<button onclick="this.form.submit();"><?php echo JText::_( 'JT_LABEL_GO' ); ?></button>
				<button onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'JT_LABEL_RESET' ); ?></button>
			</td>
			<td nowrap="nowrap">
				<?php
				echo $this->lists['appTitle'];
				?>
				<?php
				echo $this->lists['state'];
				?>
				<?php
				echo $this->lists['gendex'];
				?>
				<?php
				echo $this->lists['language'];
				?>
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
				<?php echo JHTML::_('grid.sort',  JText::_( 'JTFAMTREE_HEADING_TREE' ), 'jte.name', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>
			<th>
				<?php echo JHTML::_('grid.sort',  JText::_( 'JTFAMTREE_HEADING_APPTITLE' ), 'japp.title', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>
			<th width="20">
				<?php echo JText::_( 'JTFAMTREE_HEADING_NUMBEROFPERSONS' ); ?>
			</th>
			<th>
				<?php echo JHTML::_('grid.sort',  JText::_( 'JTFAMTREE_HEADING_GENDEX' ), 'jte.indGendex', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>
			<th>
				<?php echo JText::_( 'JTFAMTREE_HEADING_TREEHOLDERID' ); ?>
			</th>
			<th>
				<?php echo JText::_( 'JTFAMTREE_HEADING_TREEHOLDER' ); ?>
			</th>
			<th>
				<?php echo JText::_( 'JT_HEADING_PUBLISHED' ); ?>
			</th>
			<th>
				<?php echo JHTML::_('grid.sort',   JText::_( 'JT_HEADING_ACCESS' ), 'access_level', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>
			<th>
				<?php echo JHTML::_('grid.sort',   JText::_( 'JT_HEADING_THEME' ), 'theme', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>
			<th width="5%" class="nowrap">
				<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_LANGUAGE', 'language', $this->lists['order_Dir'], $this->lists['order']); ?>
			</th>
			<th width="2%">
				<?php echo JText::_( 'JT_HEADING_ID' ); ?>
			</th>
		</tr>
	</thead>

	<tfoot>
		<tr>
			<td colspan="13"><?php echo $this->pagination->getListFooter(); ?></td>
		</tr>
	</tfoot>
	
	<tbody>
	<?php
	$k = 0;
	for ($i=0, $n=count( $this->items ); $i < $n; $i++)	{
		$row = &$this->items[$i];
		$checked 	= JHTML::_('grid.id',   $i, $row->id );
		$published 	= JHTML::_('grid.published', $row, $i ); 
		$click   	= 'return listItemTask(\'cb'.$i.'\', \'edit\')';
		$lang       = ($row->language=='*') 
						? JText::alt('JALL', 'language') 
						: ($row->language_title ? $this->escape($row->language_title) : JText::_('JUNDEFINED'));	
	?>
		<tr class="<?php echo "row$k"; ?>">
			<td><?php echo $this->pagination->getRowOffset( $i ); ?></td>
			<td><?php echo $checked; ?></td>
			<td>
				<a href="javascript:void(0);" onclick="<?php echo $click; ?>" title="<?php echo JText::_( 'JTFAMTREE_TOOLTIP_EDIT' ); ?>">
					<?php echo $row->name; ?>
				</a>
			</td>
			<td><?php echo $row->appTitle; ?></td>
			<td><?php echo $row->numberOfPersons; ?></td>
			<td><?php echo JText::_(($row->indGendex == 2) ? 'JYES':  'JNO'); ?></td>
			<td><?php echo $row->root_person_id; ?></td>
			<td><?php echo $row->firstName; ?> <?php echo $row->familyName; ?></td>
			<td align="center"><?php echo $published;?></td>
			<td align="center"><?php echo $row->access_level; ?></td>
			<td align="center"><?php echo $row->theme; ?></td>
			<td class="center nowrap"><?php echo $lang; ?></td>			
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
<input type="hidden" name="controller" value="jt_familytree" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="" />

</form>