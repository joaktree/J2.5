<?php
// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

// import component libraries
JLoader::import('helper.formhelper', JPATH_COMPONENT);
?>

<script type="text/javascript">
	function jtsubmitbutton(task)
	{
		if (task == 'cancel' || document.formvalidator.isValid(document.id('media2Form'))) {
			Joomla.submitform(task, document.getElementById('media2Form'));
		} else {
			alert('<?php echo $this->escape(JText::_('JT_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<?php 
	// retrieve parameters
	$ds				= '/';
	$docsFromGedcom	= (int) $this->params->get('indDocuments', 0); 
	$pictureroot	= $this->params->get('Directory', 'images'.$ds.'joaktree');
?>

<div id="jt-form"> 
<form action="<?php echo JRoute::_('index.php?option=com_joaktree'); ?>" method="post" name="media2Form" id="media2Form" class="form-validate">
<?php echo $this->form->getInput('type', null, 'media'); ?>

<?php if (($this->lists['userAccess'])
			&& (is_object($this->item)) 
			&& (is_object($this->canDo)) && 
			(  $this->canDo->get('media.create')
			|| ($this->canDo->get('core.edit') && ($docsFromGedcom))
			)
		  ) { 
?> 
<!-- user has access to information -->
<div class="width-100 fltlft">
	<div class="jt-content-th" >
		<div class="jt-h3-th">
			<?php echo JText::_( 'JT_EDIT_RECORD' ).':&nbsp;'.$this->item->firstName.'&nbsp;'.$this->item->familyName; ?>
			<span style="float: right;">
				<?php echo (($this->lists['indLiving']) ? JText::_( 'JT_LIVING' ) : JText::_( 'JT_NOTLIVING' )); ?>
			</span>
		</div>
	</div>

	<fieldset class="joaktreeform">
		<legend><?php echo JText::_('JT_EDITPICTURES'); ?></legend>
		
		<!-- Save + cancel buttons -->
		<?php $but = array( 'save' 		=> (($docsFromGedcom) ? true : false)
						  , 'cancel' 	=> (($docsFromGedcom) ? true : false)
						  , 'check' 	=> false
						  , 'done' 		=> ((!$docsFromGedcom) ? true : false)
						  , 'add'		=> false
						  ); ?>
		<?php echo FormHelper::getButtons(1, $but) ;?>							
		<!-- End save + cancel buttons -->	
			
		<!-- Person media -->
		<?php echo $this->form->getInput('lineEnd', null, $this->lists['lineEnd']); ?>
		<?php echo $this->form->getInput('id', 'person', $this->item->id); ?>
		<?php echo $this->form->getInput('app_id', 'person', $this->lists['appId']); ?>
		<?php echo $this->form->getInput('living', 'person', $this->lists['indLiving']); ?>
		<?php echo $this->form->getInput('status', 'person', 'loaded'); ?>
				
		<?php
			// permissions are taken from media manager (=com_media)
			$this->form->setValue('asset_id', null, 'com_media'); 
			echo $this->form->getInput('asset_id', null, 'com_media'); 
		?>

		<?php if ($docsFromGedcom) { ?>
			<?php echo $this->form->getInput('status', 'person.media', '0!'.((is_object($this->picture)) ? 'loaded' : 'new')); ?>
			<?php echo $this->form->getInput('id', 'person.media', ((is_object($this->picture)) ? $this->picture->id : null)); ?>
		<?php } ?>
		
		<?php
			if ((!$docsFromGedcom) && ($this->canDo->get('media.create'))) {
				// retrieve pictures
				$pictures  = $this->item->getPictures();	
				$directory = (count($pictures)) ? $pictures[0]->directory: $pictureroot.$ds.$this->item->id;
				// Tell the user in which directory the pictures should be placed.
		?>		
				<div class="jt-high-row" style="margin-left: 10px;">
					<?php echo JText::sprintf('JT_PIC_DIRECTORY', $directory); ?>
				</div>
				<div class="jt-clearfix"></div>
		<?php 
			} 
		?>


		<ul class="joaktreeformlist">
			<?php if ($this->canDo->get('core.edit') && ($docsFromGedcom)) { ?>
				<li>
					<?php echo $this->form->getLabel('title', 'person.media'); ?>
					<?php echo $this->form->getInput('title', 'person.media', ((is_object($this->picture)) 
																				? htmlspecialchars_decode($this->picture->title, ENT_QUOTES) 
																				: null
																				)) ; ?>			
				</li>
			<?php } ?>
			<?php if ($this->canDo->get('media.create')) { ?>
				<li>
					<?php echo $this->form->getLabel('path_file', 'person.media'); ?>
					<?php echo $this->form->getInput('path_file', 'person.media', ((is_object($this->picture)) ? $this->picture->file : $pictureroot.$ds.$this->item->id.$ds)) ; ?>			
				</li>
			<?php } ?>	
		</ul>
		
		<!-- End: Person media -->
			
	</fieldset>

	<input type="hidden" name="treeId" value="<?php echo $this->lists['treeId']; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="personform" />
	<?php echo JHtml::_('form.token'); ?>

</div>

<div class="clr"></div>

<?php } else { ?>
<!-- user has NO access to information -->
	<div class="jt-content-th" >
		<div class="jt-noaccess"><?php echo JText::_( 'JT_NOACCESS' ); ?></div>
	</div>
<?php } ?>

<div class="jt-stamp">
	<?php echo $this->lists[ 'CR' ]; ?>
</div>

</form>
</div>