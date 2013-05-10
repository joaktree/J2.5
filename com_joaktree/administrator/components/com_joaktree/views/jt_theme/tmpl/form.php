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
		if (task == 'cancel' || document.formvalidator.isValid(document.id('theme-form'))) {
			submitform(task);
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
	</script>
<!-- 	window.addEvent('domready', function() {
		document.id('jform_type0').addEvent('click', function(e){
			document.id('image').setStyle('display', 'block');
			document.id('url').setStyle('display', 'block');
			document.id('custom').setStyle('display', 'none');
		});
		document.id('jform_type1').addEvent('click', function(e){
			document.id('image').setStyle('display', 'none');
			document.id('url').setStyle('display', 'none');
			document.id('custom').setStyle('display', 'block');
		});
		if(document.id('jform_type0').checked==true) {
			document.id('jform_type0').fireEvent('click');
		} else {
			document.id('jform_type1').fireEvent('click');
		}
	});
-->

<form action="<?php JRoute::_('index.php?option=com_joaktree'); ?>" method="post" name="adminForm" id="theme-form" class="form-validate">
<div class="width-50 fltlft">
	<fieldset class="adminform">
		<legend><?php echo empty($this->item->id) ? JText::_('JTTHEME_TITLE_NEWNAME') : JText::sprintf('JTTHEME_TITLE_EDITNAME', ucfirst($this->item->name)); ?></legend>
		<ul class="adminformlist">
			<li>
				<?php if (empty($this->item->id)) {
					echo $this->form->getLabel('newname');
					echo $this->form->getInput('newname');
				} else {
					echo $this->form->getLabel('name');
					echo $this->form->getInput('name');
				} ?>
			</li>

			<li><?php echo $this->form->getLabel('id'); ?>
			<?php echo $this->form->getInput('id'); ?></li>
		</ul>
		<div class="clr"> </div>
	</fieldset>
	
	<?php if (empty($this->item->id)) { ?>
		<fieldset class="adminform">
			<legend><?php echo JText::_('JTTHEME_TITLE_CSS'); ?></legend>
			<ul class="adminformlist">
				<li><?php echo $this->form->getLabel('theme'); ?>
				<?php echo $this->form->getInput('theme'); ?></li>
			</ul>	
			<div class="clr"> </div>
		</fieldset>
	<?php } ?>
</div>

<div class="width-50 fltrt">
		<fieldset class="adminform">
		<legend><?php echo JText::_('JTTHEME_TITLE_PARAMS'); ?></legend>
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

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="cid[]" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="controller" value="jt_themes" />
	<?php echo JHtml::_('form.token'); ?>
</div>

<div class="clr"></div>
</form>
