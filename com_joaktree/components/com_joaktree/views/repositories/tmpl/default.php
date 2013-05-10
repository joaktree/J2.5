<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php 
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.modal', 'a.modal');
?>

<form action="<?php echo JRoute::_($this->lists['link']); ?>" method="post" id="adminForm" name="adminForm">
<?php echo JHTML::_( 'form.token' ); ?>

<?php if ($this->lists['userAccess']) { ?> 
<!-- user has access to information -->

	<?php 
	if ( (is_object($this->canDo)) && 
			(  $this->canDo->get('core.create')  
			|| $this->canDo->get('core.edit')
			|| $this->canDo->get('core.delete')
			)
		) {
		$colspan=4;
		$indActions=true;
	} else {
		$colspan=3;
		$indActions=false;
	}
	
	?>

	<div id="jt-content">
		<table>
			<!-- header -->
			<thead>		
				<tr>
					<th colspan="<?php echo $colspan; ?>" class="jt-content-th">
						<div class="jt-h3-th" style="float: left;">
							<?php echo JText::_( 'JT_REPOSITORIES' ); ?>
						</div>
						<div style="float: right;">
							<input type="text" name="search1" id="search1" value="<?php echo $this->lists['search1'];?>" class="text_area" size="30" onchange="document.joaktreeForm.submit();" />
							<input type="submit" onclick="this.form.submit();" name="Go" class="button" value="<?php echo JText::_( 'JT_SEARCH' ); ?>" title="<?php echo JText::_( 'JT_TO_SEARCH' ); ?>" />
							<input type="submit" onclick="document.getElementById('search1').value='';this.form.submit();" name="Reset" class="button" value="<?php echo JText::_( 'JT_RESET' ); ?>" title="<?php echo JText::_( 'JT_TO_RESET' ); ?>" />
						</div>
						<div class="clearfix"></div>                               
					</th>				
				</tr>		
				<tr>
					<th class="jt-content-th" width="5" align="center">
						<?php echo JText::_( 'JT_NUM' ); ?>
					</th>
					<th class="jt-content-th">
						<div class="jt-h3-list">
							<?php echo JText::_( 'JT_LABEL_NAME' ); ?>
						</div>				
					</th>
					<th class="jt-content-th">
						<div class="jt-h3-list">
							<?php echo JText::_( 'JT_WEBSITE' ); ?>
						</div>				
					</th>
					<?php if ($indActions) {?>
						<th class="jt-content-th">
							<div class="jt-h3-list">
								<?php echo JText::_( 'JT_ACTIONS' ); ?>
							</div>				
						</th>
					<?php } ?>
				</tr>
			</thead>
			<!-- footer -->
			<tfoot>
				<tr align="center">
					<td colspan="<?php echo $colspan; ?>" >
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			<!-- table body -->
			<tbody>
			
			<?php 
			if ( (is_object($this->canDo)) && ($this->canDo->get('core.create')) ) {
			?>
				<tr class="jt-table-entry1" >
					<td style="padding: 2px 5px;">&nbsp;</td>
					<td colspan="2" style="padding: 2px 5px;">&nbsp;</td>
					<td style="padding: 2px 5px;">
						<div class="jt-edit">
							<span style="display: none;">
								<input 
									type="checkbox" 
									id="add" 
									name="cid[]" 
									value="new" 
									onclick="isChecked(this.checked);" 
								/>
							</span>
							<a 	href="#"
								onclick="return listItemTask('add', 'edit');"
								title="<?php echo JText::_('JTADD_DESC'); ?>" 
							>
								<?php echo JText::_('JTADD'); ?>
							</a>
						</div>
					</td>
				</tr>			
			<?php 
			}
			?>
			
			<!-- Show newly added item -->
			<?php if ($this->lists['status'] == 'new') { ?>
				<tr class="jt-table-entry1" >
					<td>-</td>
					<td class="jt-just-changed"><?php echo $this->newItem->name; ?></td>
					<td>
						<?php if ($this->newItem->website) { ?>
						<a href="<?php echo $this->newItem->website; ?>" target="_repository">
							<?php echo $this->newItem->website; ?>
						</a>
						<?php } else { ?>					
							&nbsp;
						<?php } ?>					
					</td>
					<?php if ($indActions) { ?>
					<td>
						<span style="display: none;">
							<input 
								type="checkbox" 
								id="newitem" 
								name="cid[]" 
								value="<?php echo $this->newItem->id; ?>" 
								onclick="isChecked(this.checked);" 
							/>
						</span>
						<?php if ($this->lists['action'] == 'select') { ?>
							<?php 
								$function =  'window.parent.jtSelectRepo_jform_app_repo_id'
											.'(\''.$this->lists['app_id'].'!'.$this->newItem->id.'\', \''
											.htmlspecialchars($this->newItem->name).'\')';
							?>					
							<span class="jt-edit">
								<a 	href="#"
									onclick="if (window.parent) <?php echo $function; ?>;"
									title="<?php echo JText::_('JTSELECT_DESC'); ?>" 
								>
									<?php echo JText::_('JTSELECT'); ?>
								</a>
							</span>	
						<?php } else { ?>
							<?php if ( (is_object($this->canDo)) && ($this->canDo->get('core.edit')) ) { ?>
								<span class="jt-edit">
									<a 	href="#"
										onclick="return listItemTask('newitem', 'edit');"
										title="<?php echo JText::_('JT_EDIT_DESC'); ?>" 
									>
										<?php echo JText::_('JT_EDIT'); ?>
									</a>
								</span>
							<?php } ?>
							<?php if ( (is_object($this->canDo)) && ($this->canDo->get('core.delete')) ) { ?>
								&nbsp;|&nbsp;
								<?php if ($this->newItem->indSource) { ?>
									<span class="jt-edit-nolink" title="<?php echo JText::_('JT_NODELETE_DESC'); ?>">
										<?php echo JText::_('JT_DELETE'); ?>							
									</span>
								<?php } else { ?>
									<span class="jt-edit">
										<a 	href="#"
											onclick="return listItemTask('newitem', 'delete');"
											title="<?php echo JText::_('JT_DELETE_DESC'); ?>" 
										>
											<?php echo JText::_('JT_DELETE'); ?>
										</a>
									</span>
								<?php } ?>
							<?php } ?>
						<?php } ?>
					</td>
					<?php } ?>
					
				</tr>
			<?php } ?>			
			<!-- End: Show newly added item -->
			
			
			<?php
			$k = 2;
			for ($i=0, $n=count( $this->items ); $i < $n; $i++)	{
				$row 		= &$this->items[$i];
				$checked 	= JHTML::_('grid.id',   $i, $row->id );
				$rowclass 	= 'jt-table-entry' . $k;
				$showclass  = ($row->id == $this->lists['repo_id']) ? 'jt-just-changed' : '';
			?>
				<tr class="<?php echo $rowclass; ?>" >
					<td align="center"><?php echo $this->pagination->getRowOffset( $i ); ?></td>
					<td class="<?php echo $showclass; ?>">
						<?php echo $row->name; ?>
					</td>
					<td>
						<?php if ($row->website) { ?>
						<a href="<?php echo $row->website; ?>" target="_repository">
							<?php echo $row->website; ?>
						</a>
						<?php } else { ?>					
							&nbsp;
						<?php } ?>					
					</td>
					
					<?php if ($indActions) { ?>
					<td>
						<span style="display: none;"><?php echo $checked; ?></span>
						<?php if ($this->lists['action'] == 'select') { ?>
							<?php 
								$function =  'window.parent.jtSelectRepo_jform_app_repo_id'
											.'(\''.$this->lists['app_id'].'!'.$row->id.'\', \''
											.htmlspecialchars($row->name).'\')';
							?>					
							<span class="jt-edit">
								<a 	href="#"
									onclick="if (window.parent) <?php echo $function; ?>;"
									title="<?php echo JText::_('JTSELECT_DESC'); ?>" 
								>
									<?php echo JText::_('JTSELECT'); ?>
								</a>
							</span>	
						<?php } else { ?>					
							<?php if ( (is_object($this->canDo)) && ($this->canDo->get('core.edit')) ) {?>
								<span class="jt-edit">
									<a 	href="#"
										onclick="return listItemTask('cb<?php echo $i; ?>', 'edit');"
										title="<?php echo JText::_('JT_EDIT_DESC'); ?>" 
									>
										<?php echo JText::_('JT_EDIT'); ?>
									</a>
								</span>
							<?php } ?>
							<?php if ( (is_object($this->canDo)) && ($this->canDo->get('core.delete')) ) {?>
								&nbsp;|&nbsp;
								<?php if ($row->indSource) { ?>
									<span class="jt-edit-nolink" title="<?php echo JText::_('JT_NODELETE_DESC'); ?>">
										<?php echo JText::_('JT_DELETE'); ?>							
									</span>
								<?php } else { ?>
									<span class="jt-edit">
										<a 	href="#"
											onclick="return listItemTask('cb<?php echo $i; ?>', 'delete');"
											title="<?php echo JText::_('JT_DELETE_DESC'); ?>" 
										>
											<?php echo JText::_('JT_DELETE'); ?>
										</a>
									</span>
								<?php } ?>
							<?php } ?>
						<?php } ?>
					</td>
					<?php } ?>
					
				</tr>
				<?php
			       $k = 3 - $k;
			}
			?>
			</tbody>
		</table>
	</div> <!-- jt-content -->
		
	<div class="jt-clearfix jt-update">
	<?php 
	    if ($this->lists[ 'showchange' ] == 1) {
			$link =  Jroute::_('index.php?&option=com_joaktree'
					.'&tmpl=component'
					.'&view=changehistory'
					.'&retId='.$this->lists[ 'retId' ]
					);
	?>
			<a class="modal" href="<?php echo $link; ?>" rel="{handler: 'iframe', size: {x: 875, y: 460}, onClose: function() {}}">
				<?php echo JText::_('JT_CHANGEHISTORY'); ?>
			</a>
	<?php } ?>
	</div>

<?php } else { ?>
<!-- user has NO access to information -->
	<div class="jt-content-th" >
		<div class="jt-noaccess"><?php echo JText::_( 'JT_NOACCESS' ); ?></div>
	</div>
<?php } ?>

<div class="jt-stamp">
	<?php echo $this->lists[ 'CR' ]; ?>
</div>

<input type="hidden" name="appId" value="<?php echo $this->lists['app_id']; ?>" />
<input type="hidden" name="action" value="<?php echo $this->lists['action']; ?>" />
<input type="hidden" name="option" value="com_joaktree" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="repository" />

</form>