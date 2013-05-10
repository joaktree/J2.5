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
		if (task == 'cancel' || document.formvalidator.isValid(document.id('tree-form'))) {
			submitform(task);
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
	</script>

<?php 
	JHTML::_('behavior.modal', 'a.modal'); 
	$name 		= 'personId'; 
	$linkPerson = 'index.php?option=com_joaktree&amp;view=jt_admin&amp;layout=element&amp;task=element&amp;tmpl=component&amp;object='.$name;
	$clrPerson  = 'window.parent.jClearPerson();'; 	
?>

 
<form action="<?php JRoute::_('index.php?option=com_joaktree'); ?>" method="post" name="adminForm" id="tree-form" class="form-validate">
<div class="width-50 fltlft">
	<fieldset class="adminform">
		<legend><?php echo is_object($this->item) ? JText::sprintf('JTTREE_TITLE_EDITNAME', ucfirst($this->item->name)) : JText::_('JTTREE_TITLE_NEWNAME'); ?></legend>

		<ul class="adminformlist">
			<!-- line 1 -->
			<li>
				<?php echo $this->form->getLabel('name'); ?>
				<?php echo $this->form->getInput('name'); ?>			
			</li>
			
			<!-- line 2 -->
			<li>
				<?php echo $this->form->getLabel('id'); ?>
				<?php echo $this->form->getInput('id'); ?>
			</li>

			<!-- line 3 -->
			<li>
				<?php echo $this->form->getLabel('app_id'); ?>
				<?php echo $this->form->getInput('app_id'); ?>
			</li>

			<!-- line 4 -->
			<li>
				<?php echo $this->form->getLabel('holds'); ?>
				<?php echo $this->form->getInput('holds'); ?>
			</li>

			<!-- line 5 -->
			<li>
				<?php echo $this->form->getLabel('personName'); ?>
				<?php echo $this->form->getInput('personName', null, (is_object($this->item)) ? $this->item->rootPersonName : null); ?>
			</li>
			<li>
				<?php echo $this->form->getLabel('root_person_id'); ?>
				<?php echo $this->form->getInput('root_person_id'); ?>
			</li>

			<!-- line 6 -->
			<li>
				<label>&nbsp;</label>
				<div class="button2-left">
					<div class="blank">
						<a class="modal" title="<?php echo JText::_('JTFIELD_PERSON_BUTTONDESC_PERSON'); ?>"  href="<?php echo $linkPerson; ?>" rel="{handler: 'iframe', size: {x: 650, y: 375}}" >
							<?php echo JText::_('JTFIELD_PERSON_BUTTON_PERSON'); ?>
						</a>
					</div>
				</div>
				<div class="button2-left">
					<div class="blank">
						<a title="<?php echo JText::_('JTTREE_TOOLTIP_CLEAR'); ?>"  onclick="<?php echo $clrPerson; ?>" >
							<?php echo JText::_('JTTREE_LABEL_CLEAR'); ?>
						</a>
					</div>
				</div>			
			</li>

			<!-- line 7 -->
			<li>
				<?php echo $this->form->getLabel('access'); ?>
				<?php echo $this->form->getInput('access'); ?>
			</li>
		</ul>
	</fieldset>
</div>


<div class="width-50 fltrt">
	<fieldset class="adminform">
		<legend><?php echo JText::_('JTTREE_TITLE_PARAMS'); ?></legend>

		<ul class="adminformlist">
			<!-- line 1 -->
			<li>
				<?php echo $this->form->getLabel('language'); ?>
				<?php echo $this->form->getInput('language'); ?>
			</li>

			<!-- line 2 -->
			<li>
				<?php echo $this->form->getLabel('theme_id'); ?>
				<?php echo $this->form->getInput('theme_id'); ?>
			</li>
		
			<!-- line 3 -->
			<li>
				<?php echo $this->form->getLabel('indGendex'); ?>
				<?php echo $this->form->getInput('indGendex'); ?>
			</li>

			<!-- line 4 -->
			<li>
				<?php echo $this->form->getLabel('indPersonCount'); ?>
				<?php echo $this->form->getInput('indPersonCount'); ?>
			</li>

			<!-- line 5 -->
			<li>
				<?php echo $this->form->getLabel('indMarriageCount'); ?>
				<?php echo $this->form->getInput('indMarriageCount'); ?>
			</li>

			<!-- line 6 -->
			<li>
				<?php echo $this->form->getLabel('robots'); ?>
				<?php echo $this->form->getInput('robots'); ?>
			</li>

			<!-- line 7 -->
			<li>
				&nbsp;
			</li>
		</ul>
		
	</fieldset>
	
	<div class="clr"> </div>


	<input type="hidden" name="task" value="" />
	<input type="hidden" name="cid[]" value="<?php echo (is_object($this->item)) ? $this->item->id : null; ?>" />
	<input type="hidden" name="controller" value="jt_familytree" />
	<?php echo JHtml::_('form.token'); ?>
	
</div>

<div class="clr"></div>

<div>
<fieldset class="adminform">
	<legend><?php echo JText::_('JTTREE_LABEL_INTROTEXT'); ?></legend>
		
	<ul class="adminformlist">
		<li>
		<?php echo $this->form->getInput('introText'); ?>
		</li>
	</ul>		
</fieldset>
</div>

<div class="clr"></div>
<?php if ($this->canDo->get('core.admin')): ?>
	<div  class="width-100 fltlft">

		<?php echo JHtml::_('sliders.start','permissions-sliders-'.((is_object($this->item)) ? $this->item->id : null), array('useCookie'=>$this->indCookie)); ?>

		<?php echo JHtml::_('sliders.panel',JText::_('JTTREE_PERMISSIONS'), 'access-rules'); ?>
			<fieldset class="panelform">
				<?php echo $this->form->getLabel('rules'); ?>
				<?php echo $this->form->getInput('rules'); ?>
			</fieldset>

		<?php echo JHtml::_('sliders.end'); ?>
	</div>
<?php endif; ?>

<div class="clr"></div>
</form>
