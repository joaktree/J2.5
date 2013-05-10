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
			<?php echo $this->form->getField('source')->save(); ?>
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
	<fieldset class="adminform">
		<legend><?php echo JText::sprintf('JTTHEME_TITLE_EDITCSS', ucfirst($this->item->name)); ?></legend>

		<div class="editor-border">
			<?php echo $this->form->getInput('source'); ?>
		</div>	
		<div class="clr"> </div>
		
		<?php echo $this->form->getLabel('sourcepath'); 
			  echo $this->form->getInput('sourcepath'); 
		?>
		<div class="clr"> </div>
	</fieldset>
	
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="cid[]" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="controller" value="jt_themes" />
	<?php echo JHtml::_('form.token'); ?>





<div class="clr"></div>
</form>
