<?php
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.tooltip');
?>

<form action="index.php?option=com_joaktree" method="post" id="adminForm" name="adminForm">
<?php echo JHTML::_( 'form.token' ); ?>

<?php 
	$shownColumns = 8;
	if ($this->columns['app'] ) { $classApp  = 'jt-show-app' ; $shownColumns++; } else { $classApp  = 'jt-hide-app' ; }
	if ($this->columns['pat'] ) { $classPat  = 'jt-show-pat' ; $shownColumns++; } else { $classPat  = 'jt-hide-pat' ; }
	if ($this->columns['per'] ) { $classPer  = 'jt-show-per' ; $shownColumns++; } else { $classPer  = 'jt-hide-per' ; }
	if ($this->columns['tree']) { $classTree = 'jt-show-tree'; $shownColumns++; } else { $classTree = 'jt-hide-tree'; }
	if ($this->columns['rob'] ) { $classRob  = 'jt-show-rob' ; $shownColumns++; } else { $classRob  = 'jt-hide-rob' ; }
	if ($this->columns['map'] ) { $classMap  = 'jt-show-map' ; $shownColumns++; } else { $classMap  = 'jt-hide-map' ; }
?>

<div id="editcell">
	<table class="adminlist">
	<thead>
		<tr>
			<th id="header" colspan="<?php echo $shownColumns; ?>" >
				<a href="#" onclick="javascript:jt_toggle('th','app');jt_toggle('td','app');">
					<?php echo JText::_('JTPERSONS_HEADING_APPTITLE'); ?>
				</a>&nbsp;|&nbsp;
				<?php if ($this->lists['patronym']) { ?>
					<a href="#" onclick="javascript:jt_toggle('th','pat');jt_toggle('td','pat');">
						<?php echo JText::_('JTPERSONS_HEADING_PATRONYM'); ?>
					</a>&nbsp;|&nbsp;
				<?php } ?>
				<a href="#" onclick="javascript:jt_toggle('th','per');jt_toggle('td','per');">
					<?php echo JText::_('JTPERSONS_HEADING_PERIOD'); ?>
				</a>&nbsp;|&nbsp;
				<a href="#" onclick="javascript:jt_toggle('th','tree');jt_toggle('td','tree');">
					<?php echo JText::_('JTPERSONS_HEADING_DEFTREE'); ?>
				</a>&nbsp;|&nbsp;
				<a href="#" onclick="javascript:jt_toggle('th','map');jt_toggle('td','map');">
					<?php echo JText::_('JT_HEADING_MAP'); ?>
				</a>&nbsp;|&nbsp;
				<a href="#" onclick="javascript:jt_toggle('th','rob');jt_toggle('td','rob');">
					<?php echo JText::_('JFIELD_METADATA_ROBOTS_LABEL'); ?>
				</a>
			</th>
		</tr>
		<tr>
			<th width="5"><?php echo JText::_( 'JT_HEADING_NUMBER' ); ?></th>
			<th width="20">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->items ); ?>);" />
			</th>			
			<th class="<?php echo $classApp; ?>">
				<?php echo JText::_( 'JTPERSONS_HEADING_APPTITLE' ); ?>
				<hr/>
				<?php echo $this->lists['appTitle']; ?>
			</th>
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
			   <th class="<?php echo $classPat; ?>">
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
			<th class="<?php echo $classPer; ?>">
				<?php echo JHTML::_('grid.sort',  'JTPERSONS_HEADING_PERIOD', '13', $this->lists['order_Dir'], $this->lists['order'] ); ?>
				<hr/>
				&nbsp;
			</th>
			<th class="<?php echo $classTree; ?>">
				<?php echo JText::_( 'JTPERSONS_HEADING_DEFTREE' ); ?>
				<hr/>
				<?php echo $this->lists['tree']; ?>
			</th>
			<th>
				<?php echo JText::_( 'JT_HEADING_PUBLISHED' ); ?>
				<hr/>
				<?php echo $this->lists['state']; ?>
			</th>
			<th>
				<?php echo JText::_( 'JT_HEADING_LIVING' ); ?>
				<hr/>
				<?php echo $this->lists['living']; ?>
			</th>
			<th >
				<?php echo JText::_( 'JT_HEADING_PAGE' ); ?>
				<hr/>
                <?php echo $this->lists['page']; ?>
			</th>
			<th class="<?php echo $classMap; ?>">
				<?php echo JText::_( 'JT_HEADING_MAP' ); ?>
				<hr/>
                <?php echo $this->lists['map']; ?>
			</th>
			<th class="<?php echo $classRob; ?>" title="<?php echo JText::_('JFIELD_METADATA_ROBOTS_DESC'); ?>">
				<?php echo JText::_( 'JFIELD_METADATA_ROBOTS_LABEL' ); ?>
				<hr/>
                <?php echo $this->lists['robots']; ?>
			</th>
		</tr>
	</thead>
	
	<tbody>
	<?php
		$k = 0;
		for ($i=0, $n=count( $this->items ); $i < $n; $i++) {
			$row 		= &$this->items[$i];
			$checked 	= JHTML::_('grid.id',   $i, $row->app_id.'!'.$row->id );
			$published 	= JHTML::_('grid.published', $row, $i ); 
			
			$clickLiving   	= 'return listItemTask(\'cb'.$i.'\', \'updateLiving\')';
			if ($row->living) {
				$living = JHTML::_( 'image.administrator', 'icon-16-true.png', 'components/com_joaktree/assets/images/','','', JText::_( 'JT_FILTER_VAL_LIVING' ));
			} else {
				$living = JHTML::_( 'image.administrator', 'icon-16-false.png', 'components/com_joaktree/assets/images/','','', JText::_( 'JT_FILTER_VAL_NOTLIVING' ));
			}
			
			$clickPage   = 'return listItemTask(\'cb'.$i.'\', \'updatePage\')';
			if ($row->page) {
				$page = JHTML::_( 'image.administrator', 'icon-16-true.png', 'components/com_joaktree/assets/images/','','', JText::_( 'JT_FILTER_VAL_PAGE' ));
			} else {
				$page = JHTML::_( 'image.administrator', 'icon-16-false.png', 'components/com_joaktree/assets/images/','','', JText::_( 'JT_FILTER_VAL_NOPAGE' ));
			}
			
			$map		 = '<select id="map'.$row->id.'" name="map'.$row->id.'" class="inputbox" onchange="javascript:jtsaveaccess(\'cb'.$i.'\')">';
			$map		.= JHtml::_('select.options', $this->maps, 'value', 'text', $row->map);
			$map		.= '</select>';	
			
			$robot		 = '<select id="robot'.$row->id.'" name="robot'.$row->id.'" class="inputbox" onchange="javascript:jtsaveaccess(\'cb'.$i.'\')">';
			$robot		.= JHtml::_('select.options', $this->robots, 'value', 'text', $row->robots);
			$robot		.= '</select>';	
			
	?>
		<tr class="<?php echo "row$k"; ?>">
			<td><?php echo $this->pagination->getRowOffset( $i ); ?></td>
			<td><?php echo $checked; ?></td>
			<td class="<?php echo $classApp; ?>"><?php echo $row->appTitle; ?></td>
			<td><?php echo $row->id; ?></td>
			<td><?php echo $row->firstName; ?></td>
			<?php if ($this->lists['patronym']) { ?>
			   <td class="<?php echo $classPat; ?>"><?php echo $row->patronym; ?></td>
			<?php } ?>
			<td><?php echo $row->familyName; ?></td>
			<td class="<?php echo $classPer; ?>" align="center"><?php echo $row->period; ?></td>
			<td class="<?php echo $classTree; ?>"><?php echo $row->familyTree; ?></td>
			<td align="center"><?php echo $published;?></td>
			<td align="center">
				<a href="javascript:void(0);" onclick="<?php echo $clickLiving; ?>" title="<?php echo JText::_( 'JT_HEADING_LIVING' ); ?>">
					<?php echo $living; ?>
				</a>
			</td>
			<td align="center">
				<a href="javascript:void(0);" onclick="<?php echo $clickPage; ?>" title="<?php echo JText::_( 'JT_HEADING_PAGE' ); ?>">
					<?php echo $page; ?>
				</a>
			</td>
			<td class="<?php echo $classMap; ?>"><?php echo $map; ?></td>
			<td class="<?php echo $classRob; ?>"><?php echo $robot; ?></td>
		</tr>
	<?php
			$k = 1 - $k;
		}
	?>
        </tbody>
	
	<tfoot>
		<tr>
			<td id="footer" colspan="<?php echo $shownColumns; ?>">
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