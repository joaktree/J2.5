<?php
// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.DS.'helpers'.DS.'html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
?>
<script type="text/javascript">
	function submitbutton(task)
	{
		if (task == 'cancel' || document.formvalidator.isValid(document.id('location-form'))) {
			submitform(task);
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}

	function clearResultValue() {
		document.getElementById('jform_resultValue').value  = null;
		document.getElementById('jform_resultValue2').value = null;
	}

	function setResult(lat,lon,adr) {
		document.getElementById('jform_latitude').value     = lat;
		document.getElementById('jform_longitude').value    = lon;
		document.getElementById('jform_resultValue').value  = adr;
		document.getElementById('jform_resultValue2').value = adr;
	}
	function setDeleteCheckbox() {
		var El = document.getElementById('jform_indDeleted');
		if (El.checked == true) { El.value = 1; }
			else { El.value = 0; }
		clearResultValue();
	}
</script>

<form action="<?php JRoute::_('index.php?option=com_joaktree'); ?>" method="post" name="adminForm" id="location-form" class="form-validate">
<div class="width-40 fltlft">
	<fieldset class="adminform">
		<legend><?php echo  $this->item->value; ?></legend>
		<ul class="adminformlist">

			<li><?php echo $this->form->getLabel('value'); ?>
			<?php echo $this->form->getInput('value'); ?></li>

			<li><?php echo $this->form->getLabel('resultValue2'); ?>
			<?php echo $this->form->getInput('resultValue2'); ?>
			<?php echo $this->form->getInput('resultValue'); ?>
			</li>

			<li><?php echo $this->form->getLabel('latitude'); ?>
			<?php echo $this->form->getInput('latitude'); ?></li>

			<li><?php echo $this->form->getLabel('longitude'); ?>
			<?php echo $this->form->getInput('longitude'); ?></li>

			<li><?php echo $this->form->getLabel('indDeleted'); ?>
			<?php echo $this->form->getInput('indDeleted'); ?></li>

			<li><?php echo $this->form->getLabel('id'); ?>
			<?php echo $this->form->getInput('id'); ?></li>
			
		</ul>
		<div class="clr"> </div>
	</fieldset>	
</div>

<?php if (count($this->geoCodeSet)) {?>
	<div>		
		<fieldset class="adminform">
		<table class="adminlist">
		<thead>
			<tr>
				<th width="5"><?php echo JText::_( 'JT_HEADING_NUMBER' ); ?></th>	
				<th><?php echo JText::_( 'JT_LABEL_GEOCODELOCATION' ); ?></th>
				<th><?php echo JText::_( 'JT_LABEL_LATITUDE' ); ?></th>
				<th><?php echo JText::_( 'JT_LABEL_LONGITUDE' ); ?></th>
			</tr>
		</thead>
		
		<tbody>
			<?php $k = 0; $i=1; ?>
			<?php foreach ($this->geoCodeSet as $result) {
					$function = 'setResult(\''.$result->lat.'\', \''.$result->lon.'\', \''.htmlspecialchars($result->adr, ENT_QUOTES).'\');'; 
			
			?>
				<tr class="<?php echo "row$k"; ?>">
					<td><?php echo $i++; ?></td>
					<td><a onclick="<?php echo $function; ?>" href="javascript:void(0);">
					    <?php echo $result->adr; ?>
					    </a>
					</td>
					<td><?php echo $result->lat; ?></td>
					<td><?php echo $result->lon; ?></td>				
				</tr>
				<?php $k = 1 - $k; ?>
			<?php } ?>
		</tbody>
		</table>
	
		<div class="clr"> </div>
		</fieldset>
	</div>
<?php } ?>	
<div class="clr"></div>

<!-- The number of hits -->
<?php echo $this->form->getInput('results', '', count($this->geoCodeSet)); ?>


<div>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="cid[]" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="controller" value="jt_locations" />
	<?php echo JHtml::_('form.token'); ?>
</div>	

<div class="clr"></div>
</form>
