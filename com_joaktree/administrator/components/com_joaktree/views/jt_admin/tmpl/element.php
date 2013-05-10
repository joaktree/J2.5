<?php
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.tooltip');
?>

<form action="index.php?option=com_joaktree&amp;view=jt_admin&amp;layout=element&amp;task=element&amp;tmpl=component&amp;object=id" method="post" id="adminForm" name="adminForm">
<?php echo JHTML::_( 'form.token' ); ?>

<div id="editcell">
	<table class="adminlist">
	<thead>
		<tr>
			<th width="5"><?php echo JText::_( 'JT_HEADING_NUMBER' ); ?></th>
			<th width="5">				
				<?php echo JHTML::_('grid.sort',  'JT_HEADING_ID', 'jpn.id', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>
			<th>
				<?php echo JHTML::_('grid.sort',  'JTPERSONS_HEADING_FIRSTNAME', 'jpn.firstName', $this->lists['order_Dir'], $this->lists['order'] ); ?>
                <hr/>
				<input type="text" name="search1" id="search1" value="<?php echo $this->lists['search1'];?>" class="text_area" onchange="document.adminForm.submit();" />
				<button onclick="this.form.submit();"><?php echo JText::_( 'JT_LABEL_GO' ); ?></button>
				<button onclick="document.getElementById('search1').value='';this.form.submit();"><?php echo JText::_( 'JT_LABEL_RESET' ); ?></button>
			</th>
			<?php if ($this->lists['patronym']) { ?>
			   <th>
				<?php echo JHTML::_('grid.sort',  'JTPERSONS_HEADING_PATRONYM', 'jpn.patronym', $this->lists['order_Dir'], $this->lists['order'] ); ?>
                                <hr/>                                
				<input type="text" name="search2" id="search2" value="<?php echo $this->lists['search2'];?>" class="text_area" onchange="document.adminForm.submit();" />
				<button onclick="this.form.submit();"><?php echo JText::_( 'JT_LABEL_GO_SHORT' ); ?></button>
				<button onclick="document.getElementById('search2').value='';this.form.submit();"><?php echo JText::_( 'JT_LABEL_RESET_SHORT' ); ?></button>
			   </th>
			<?php } ?>
			<th>
				<?php echo JHTML::_('grid.sort',  'JTPERSONS_HEADING_FAMNAME', 'jpn.familyName', $this->lists['order_Dir'], $this->lists['order'] ); ?>
                                <hr/>
                                <input type="text" name="search3" id="search3" value="<?php echo $this->lists['search3'];?>" class="text_area" onchange="document.adminForm.submit();" />
				<button onclick="this.form.submit();"><?php echo JText::_( 'JT_LABEL_GO_SHORT' ); ?></button>
				<button onclick="document.getElementById('search3').value='';this.form.submit();"><?php echo JText::_( 'JT_LABEL_RESET_SHORT' ); ?></button>
			</th>
			<th>
				<?php echo JText::_( 'BIRT' ); ?>
				<hr/>
				&nbsp;
			</th>
			<th>
				<?php echo JText::_( 'JTPERSONS_HEADING_APPTITLE' ); ?>
				<hr/>
				<?php echo $this->lists['appTitle']; ?>
			</th>
			<th>
				<?php echo JText::_( 'JTPERSONS_HEADING_DEFTREE' ); ?>
				<hr/>
				<?php echo $this->lists['tree']; ?>
			</th>
		</tr>
	</thead>
	
	<tbody>
	<?php
		$k = 0;
		for ($i=0, $n=count( $this->items ); $i < $n; $i++) {
			$row 	= &$this->items[$i];
			$name   = $row->firstName.' '.$row->familyName;
			$linkname  = str_replace("'", "\&#39;", $name);
			$appTitle  = str_replace("'", "\&#39;", $row->appTitle);
			$link 	= 'window.parent.jSelectPerson(\''.$row->id.'\', \''.$linkname.'\', \''.$row->app_id.'\', \''.$appTitle.'\', \''.$row->default_tree_id.'\');';
			$anker  = 'style="cursor: pointer;" onclick="'.$link.'"';
	?>
		<tr class="<?php echo "row$k"; ?>">
			<td><?php echo $this->pagination->getRowOffset( $i ); ?></td>
			<td><a <?php echo $anker ?>><?php echo $row->id; ?></a></td>
			<td><a <?php echo $anker ?>><?php echo $row->firstName; ?></a></td>
			<?php if ($this->lists['patronym'] != 0) { ?>
			   <td><a <?php echo $anker ?>><?php echo $row->patronym; ?></a></td>
			<?php } ?>
			<td><a <?php echo $anker ?>><?php echo $row->familyName; ?></a></td>
			<td align="center"><?php echo $row->birthDate; ?></td>
			<td><?php echo $row->appTitle; ?></td>
			<td><?php echo $row->familyTree; ?></td>
		</tr>
	<?php
			$k = 1 - $k;
		}
	?>
        </tbody>
	
	<tfoot>
		<?php 
			if ($this->lists['patronym'] != 0) {
				$colspanValue = 11;
			} else {
				$colspanValue = 10;
			} 
		?>
		<tr>
			<td colspan="<?php echo $colspanValue; ?>">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
	</tfoot>
	</table>
</div>


<input type="hidden" name="option" value="com_joaktree" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="jt_admin" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="" />

</form>