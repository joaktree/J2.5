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
		if (task == 'cancel' || document.formvalidator.isValid(document.id('application-form'))) {
			submitform(task);
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
	</script>

<form action="<?php JRoute::_('index.php?option=com_joaktree'); ?>" method="post" name="adminForm" id="application-form" class="form-validate">
<div class="width-50 fltlft">
	<fieldset class="adminform">
		<legend><?php echo empty($this->item->id) ? JText::_('JTAPPS_TITLE_NEWNAME') : JText::sprintf('JTAPPS_TITLE_EDITNAME', ucfirst($this->item->title)); ?></legend>
		<ul class="adminformlist">

			<li><?php echo $this->form->getLabel('title'); ?>
			<?php echo $this->form->getInput('title'); ?></li>

			<li><?php echo $this->form->getLabel('description'); ?>
			<?php echo $this->form->getInput('description'); ?></li>

			<li><?php echo $this->form->getLabel('programName'); ?>
			<?php echo $this->form->getInput('programName'); ?></li>

			<li><?php echo $this->form->getLabel('id'); ?>
			<?php echo $this->form->getInput('id'); ?></li>
			
		</ul>
		<div class="clr"> </div>
	</fieldset>	
</div>

<div class="width-50 fltrt">
		<fieldset class="adminform">
		<legend><?php echo JText::_('JTAPPS_TITLE_PARAMS'); ?></legend>
		<ul class="adminformlist">
			<?php foreach($this->form->getFieldset('settings') as $field): ?>
				<li>
					<?php if (!$field->hidden): ?>
						<?php echo $field->label; ?>
					<?php endif; ?>
					<?php echo $field->input; ?>
				</li>
			<?php endforeach; ?>
			</ul>
		</fieldset>
</div>
<div class="clr"></div>

<?php if ($this->canDo->get('core.admin')): ?>
	<div  class="width-100 fltlft">

		<?php echo JHtml::_('sliders.start','permissions-sliders-'.$this->item->id, array('useCookie'=>$this->indCookie)); ?>

		<?php echo JHtml::_('sliders.panel',JText::_('JTAPPS_PERMISSIONS'), 'access-rules'); ?>
			<fieldset class="panelform">
				<?php echo $this->form->getLabel('rules'); ?>
				<?php echo $this->form->getInput('rules'); ?>
			</fieldset>

		<?php echo JHtml::_('sliders.end'); ?>
	</div>
<?php endif; ?>
<div class="clr"></div>

<div>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="cid[]" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="controller" value="jt_applications" />
	<?php echo JHtml::_('form.token'); ?>
</div>	

<div class="clr"></div>
</form>
