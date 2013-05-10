<?php
defined('_JEXEC') or die('Restricted access');

//Ordering allowed ?
$ordering = ($this->lists['order'] == 'jte.id');

JHTML::_('behavior.tooltip');
?>
<?php 
	$staticmapAPIkey  = (isset($this->mapSettings->staticmap)) ? $this->mapSettings->staticmap.'APIkey' : '';
	$interactivemapAPIkey = (isset($this->mapSettings->interactivemap)) ? $this->mapSettings->interactivemap.'APIkey' : '';
	$geocodeAPIkey    = (isset($this->mapSettings->geocode)) ? $this->mapSettings->geocode.'APIkey' : '';
?>

<?php if (  (empty($this->mapSettings->staticmap))
		 || ((!empty($this->mapSettings->staticmap)) && isset($this->mapSettings->$staticmapAPIkey) && empty($this->mapSettings->$staticmapAPIkey))
		 || (empty($this->mapSettings->interactivemap))		 
		 || ((!empty($this->mapSettings->interactivemap))&& isset($this->mapSettings->$interactivemapAPIkey) && empty($this->mapSettings->$interactivemapAPIkey))		 
		 || (empty($this->mapSettings->geocode)) 
		 || ((!empty($this->mapSettings->geocode))   && isset($this->mapSettings->$geocodeAPIkey) && empty($this->mapSettings->$geocodeAPIkey))		 
		 || ($this->mapSettings->invalidpc  >  5)
		 ) {
?>
<form><fieldset class="adminform">
	<legend><?php echo JText::_('JTMAP_TITLE_PARAMS');?></legend>
	<table class="adminlist">
		<tr><th width="15%"><?php echo JText::_('MBJ_STATICMAP'); ?></th>
		    <td width="25%"><?php echo (empty($this->mapSettings->staticmap))
		    							? '<strong style="color: red">'.JText::_('JNO').'</strong>'
		    							: ucfirst($this->mapSettings->staticmap); ?>
			</td>
		    <th width="15%"><?php echo JText::_('COM_JOAKTREE_API_LABEL'); ?></th>
		    <td><?php echo (isset($this->mapSettings->$staticmapAPIkey) && !empty($this->mapSettings->$staticmapAPIkey)) 
		    				? JText::_('JYES')
		    				: ( (!isset($this->mapSettings->$staticmapAPIkey))
		    				  ? JText::_('...')
		    				  : '<strong style="color: red">'.JText::_('JNO').'</strong>'
		    				  );
		    	?>
		    </td>
		</tr>
		<tr><th width="15%"><?php echo JText::_('MBJ_INTERACTIVEMAP'); ?></th>
		    <td width="25%"><?php echo (empty($this->mapSettings->interactivemap))
		    							? '<strong style="color: red">'.JText::_('JNO').'</strong>'
		    							: ucfirst($this->mapSettings->interactivemap); ?>
			</td>
		    <th width="15%"><?php echo JText::_('COM_JOAKTREE_API_LABEL'); ?></th>
		    <td><?php echo (isset($this->mapSettings->$interactivemapAPIkey) && !empty($this->mapSettings->$interactivemapAPIkey)) 
		    				? JText::_('JYES')
		    				: ( (!isset($this->mapSettings->$interactivemapAPIkey))
		    				  ? JText::_('...')
		    				  : '<strong style="color: red">'.JText::_('JNO').'</strong>'
		    				  );
		    	?>
		    </td>
		</tr>
		<tr><th width="15%"><?php echo JText::_('MBJ_GEOCODE'); ?></th>
		    <td width="25%"><?php echo (empty($this->mapSettings->geocode))
		    							? '<strong style="color: red">'.JText::_('JNO').'</strong>'
		    							: ucfirst($this->mapSettings->geocode); ?>
			</td>
		    <th width="15%"><?php echo JText::_('COM_JOAKTREE_API_LABEL'); ?></th>
		    <td><?php echo (isset($this->mapSettings->$geocodeAPIkey) && !empty($this->mapSettings->$geocodeAPIkey)) 
		    				? JText::_('JYES')
		    				: ( (!isset($this->mapSettings->$geocodeAPIkey))
		    				  ? JText::_('...')
		    				  : '<strong style="color: red">'.JText::_('JNO').'</strong>'
		    				  );
		    	?>
		    </td>
		</tr>
		<tr><th width="15%"><?php echo JText::_('JT_NUM_LOCATIONS'); ?></th>
		    <td width="25%"><?php echo $this->mapSettings->total; ?></td>
		    <th width="15%"><?php echo JText::_('JT_NUM_INVALIDLOCATIONS'); ?></th>
		    <td><?php if ($this->mapSettings->invalidpc > 20) { ?>
		    		<strong style="color: red"><?php } ?>	    
		    		<?php echo $this->mapSettings->invalid.'&nbsp;('.$this->mapSettings->invalidpc.'%)'; ?>
		    	<?php if ($this->mapSettings->invalidpc > 20) { ?>
		    		</strong><?php } ?>
		    </td>
		</tr>
		<tr><th width="15%"><?php echo JText::_('MBJ_LABEL_COUNTRY'); ?></th>
		    <td width="25%"><?php echo (isset($this->mapSettings->country)) ? $this->mapSettings->country : ''; ?></td>
		    <th width="15%"><?php echo JText::_('MBJ_LABEL_LANGUAGE'); ?></th>
		    <td><?php echo (isset($this->mapSettings->language)) ? $this->mapSettings->language : ''; ?></td>
		</tr>
		<tr><th width="15%"><?php echo JText::_('MBJ_LABEL_LOADSIZE'); ?></th>
		    <td width="25%"><?php echo (isset($this->mapSettings->maxloadsize)) ? $this->mapSettings->maxloadsize : ''; ?></td>
		    <th width="15%"><?php echo JText::_('MBJ_LABEL_INDHTTPS'); ?></th>
		    <td><?php echo (isset($this->mapSettings->indHttps) && $this->mapSettings->indHttps) ? JText::_('JYES') : JText::_('JNO'); ?></td>
		</tr>
	</table>
</fieldset></form>
<div>&nbsp;<br />&nbsp;</div>
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
			<td colspan="8"><?php echo $this->pagination->getListFooter(); ?></td>
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
						<?php echo $this->escape($row->name); ?>
					</a>
				<?php else : ?>
						<?php echo $this->escape($row->name); ?>
				<?php endif; ?>
			</td>
			<td><?php echo $this->escape($row->selection);?>&nbsp;/&nbsp;<?php echo $this->escape($row->service);?></td>
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