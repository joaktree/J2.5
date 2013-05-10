<?php
// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.DS.'helpers'.DS.'html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.modal', 'a.modal'); 
$name 		= 'personId'; 
$linkPerson = 'index.php?option=com_joaktree&amp;view=jt_admin&amp;layout=element&amp;task=element&amp;tmpl=component&amp;object='.$name;
$clrPerson  = 'window.parent.jClearPerson();'; 	

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
	</script>

<form action="<?php JRoute::_('index.php?option=com_joaktree'); ?>" method="post" name="adminForm" id="location-form" class="form-validate">
<div class="width-50 fltlft">
	<fieldset class="adminform">
		<legend><?php echo (!is_object($this->item) || ((is_object($this->item)) && (!$this->item->id)) ) 
						? JText::_('JTMAP_TITLE_NEWNAME')
						: JText::sprintf('JTMAP_TITLE_EDITNAME', ucfirst($this->item->name)); ?></legend>
		<ul class="adminformlist">

			<li><?php echo $this->form->getLabel('name'); ?>
			<?php echo $this->form->getInput('name'); ?></li>

			<li><?php echo $this->form->getLabel('selection'); ?>
			<?php echo $this->form->getInput('selection'); ?></li>
			
			<?php switch ($this->item->selection) {
				    case "person"	  : $classPerson     = 'jt-show';
				    					$classTree 	     = 'jt-hide';
				    					$classLocation   = 'jt-hide';
				    					break;
				    case "location"	  : $classPerson     = 'jt-hide';
				    					$classTree 	     = 'jt-show';
				    					$classLocation   = 'jt-show';
				    					break;
				    case "tree"		  : 
					default			  : $classPerson     = 'jt-hide';
										$classTree 	     = 'jt-show';
				    					$classLocation   = 'jt-hide';
										break; 
				  }
			?>

			<li id="tree" class="<?php echo $classTree; ?>">
				<?php echo $this->form->getLabel('tree'); ?>
				<?php echo $this->form->getInput('tree', null, (is_object($this->item)) ? $this->item->tree_id : null); ?>
			</li>

			
			<li id="descendants" class="<?php echo $classTree; ?>">
				<?php echo $this->form->getLabel('descendants'); ?>
				<?php echo $this->form->getInput('descendants'); ?>
			</li>
			
			<li id="familyName" class="<?php echo $classTree; ?>">
				<?php echo $this->form->getLabel('familyName'); ?>
				<?php echo $this->form->getInput('familyName', null, (is_object($this->item)) ? $this->item->subject : null); ?>
			</li>
			
			<!--  person  -->
			<li id="person1" class="<?php echo $classPerson; ?>">
				<?php echo $this->form->getLabel('personName'); ?>
				<?php echo $this->form->getInput('personName', null, (is_object($this->item)) ? $this->item->personName : null); ?>
				<?php echo $this->form->getInput('root_person_id', null, (is_object($this->item)) ? $this->item->person_id : null); ?>
				<?php echo $this->form->getInput('app_id'); ?>
			</li>

			<li id="person2" class="<?php echo $classPerson; ?>">
				<label>&nbsp;</label>
				<div class="button2-left">
					<div class="blank">
						<a class="modal" href="<?php echo $linkPerson; ?>" rel="{handler: 'iframe', size: {x: 650, y: 375}}" >
							<?php echo JText::_('JTFIELD_PERSON_BUTTON_PERSON'); ?>
						</a>
					</div>
				</div>
				<div class="button2-left">
					<div class="blank">
						<a onclick="<?php echo $clrPerson; ?>" >
							<?php echo JText::_('JGLOBAL_SELECTION_NONE'); ?>
						</a>
					</div>
				</div>			
			</li>
			<li id="relations" class="<?php echo $classPerson; ?>">
				<?php echo $this->form->getLabel('person_relations'); ?>
				<?php echo $this->form->getInput('person_relations'); ?>
			</li>
			<!--  End person  -->	

			<li><?php echo $this->form->getLabel('period_start'); ?>
			<?php echo $this->form->getInput('period_start'); ?></li>

			<li><?php echo $this->form->getLabel('period_end'); ?>
			<?php echo $this->form->getInput('period_end'); ?></li>

			<li><?php echo $this->form->getLabel('events'); ?>
			<?php echo $this->form->getInput('events'); ?></li>

			<li><?php echo $this->form->getLabel('id'); ?>
			<?php echo $this->form->getInput('id'); ?></li>
			
		</ul>
		<div class="clr"> </div>
	</fieldset>	
</div>

<div class="width-50 fltrt">
	<?php echo JHtml::_('sliders.start', 'map-sliders'); ?>
	
	<?php echo JHtml::_('sliders.panel', JText::_('JTMAP_TITLE_PARAMS'), 'map-params'); ?>
	<fieldset class="panelform">
	<ul class="adminformlist">
		<li><?php echo $this->form->getLabel('service'); ?>
		<?php echo $this->form->getInput('service'); ?></li>				
	
		<?php foreach($this->form->getFieldset('settings') as $field): ?>
			<li <?php echo (($field->id == 'jform_params_distance') ? 'id="distance" class="'.$classLocation.'"' : ''); ?> >
				<?php if (!$field->hidden): ?>
					<?php echo $field->label; ?>
				<?php endif; ?>
				<?php echo $field->input; ?>
			</li>
		<?php endforeach; ?>
		</ul>
	</fieldset>
		
	<?php echo JHtml::_('sliders.panel', JText::_('JTMAP_TITLE_ADVPARAMS'), 'map-adv-params'); ?>
	<fieldset class="panelform">
	<ul class="adminformlist">
	
		<?php foreach($this->form->getFieldset('adv-settings') as $field): ?>
			<li>
				<?php if (!$field->hidden): ?>
					<?php echo $field->label; ?>
				<?php endif; ?>
				<?php echo $field->input; ?>
			</li>
		<?php endforeach; ?>
		</ul>
	</fieldset>
		
	<?php echo JHtml::_('sliders.end'); ?>
	
</div>

<div class="clr"></div>


<div>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="cid[]" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="controller" value="jt_maps" />
	<?php echo JHtml::_('form.token'); ?>
</div>	

<div class="clr"></div>
</form>
