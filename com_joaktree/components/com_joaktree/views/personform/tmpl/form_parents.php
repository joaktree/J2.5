<?php
// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.modal');

// import component libraries
JLoader::import('helper.formhelper', JPATH_COMPONENT);

?>

<script type="text/javascript">
	function jtsubmitbutton(task)
	{
		if (task == 'cancel' || document.formvalidator.isValid(document.id('parentForm'))) {
			Joomla.submitform(task, document.getElementById('parentForm'));
		} else {
			jtrefnot(1);
			alert('<?php echo $this->escape(JText::_('JT_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>
<script type="text/javascript">
	<?php echo FormHelper::getGeneralRowScript(); ?>
</script>


<div id="jt-form"> 
<form action="<?php echo JRoute::_('index.php?option=com_joaktree'); ?>" method="post" name="parentForm" id="parentForm" class="form-validate">
<?php echo $this->form->getInput('type', null, 'parents'); ?>

<?php if (($this->lists['userAccess']) && (is_object($this->canDo)) && ($this->canDo->get('core.edit')) ) { ?> 
<!-- user has access to information -->
<div class="width-100 fltlft">
	<div class="jt-content-th" >
		<div class="jt-h3-th">
			<?php if (!is_object($this->item)) {
					echo JText::_( 'JT_NEW_PERSON' );
				  } else {
				  	echo JText::_( 'JT_EDIT_RECORD' ).':&nbsp;'.$this->item->firstName.'&nbsp;'.$this->item->familyName;
			?>
					<span style="float: right;">
						<?php echo (($this->lists['indLiving']) ? JText::_( 'JT_LIVING' ) : JText::_( 'JT_NOTLIVING' )); ?>
					</span>
			<?php 
				  }
			 ?>
		</div>
	</div>

	<fieldset class="joaktreeform">
		<legend><?php echo JText::_('JT_EDITPARENTS'); ?></legend>
		
		<!-- Save + cancel buttons -->
		<?php echo FormHelper::getButtons(1) ;?>							
		<!-- End save + cancel buttons -->		

		<?php echo $this->form->getInput('lineEnd', null, $this->lists['lineEnd']); ?>
		<?php echo $this->form->getInput('id', 'person', ((is_object($this->item)) ? $this->item->id : null)); ?>
		<?php echo $this->form->getInput('app_id', 'person', $this->lists['appId']); ?>
		<?php echo $this->form->getInput('living', 'person', $this->lists['indLiving']); ?>
		<?php echo $this->form->getInput('status', 'person', 'relation' ); ?>
		
		<!-- Relations -->	
		<table style="width: 96%;">
			<!-- header for relations -->
			<thead>
				<tr>
					<th class="jt-content-th">
						<?php echo JText::_( 'JT_PARENT' ); ?>
					</th>
					<th class="jt-content-th">
						<?php echo JText::_( 'JT_LABEL_NAME' ); ?>
					</th>
					<th class="jt-content-th">
						<?php echo JText::_( 'JT_BORN' ); ?>
					</th>
					<th class="jt-content-th">
						<?php echo JText::_( 'JT_DIED' ); ?>
					</th>
					<th class="jt-content-th">
						<?php echo JText::_( 'JT_LABEL_RELATIONTYPE' ); ?>
					</th>
					<th class="jt-content-th">
						<?php echo JText::_( 'JT_ACTIONS' ); ?>
					</th>						
				</tr>
			</thead>
			<!-- End: header for relations -->
			
			<!-- tabel body for relations -->
			<?php $tabRelId = 're_'; ?>			
			<tbody id="<?php echo $tabRelId; ?>">
				<!-- List of existing relations -->
				<?php
				if (is_object($this->item)) { 
		  			$fathers = $this->item->getFathers();
		  			$mothers = $this->item->getMothers();
		  			$parents = array_merge($fathers, $mothers);
		  			if (count($parents)) {
						$k = 4;
						for ($i=0, $n=count($parents); $i < $n; $i++)	{
							$parent = &$parents[$i];
							$parent->orderNumber = $i+1;													 	
				?>
							<!-- Row for one existing relation -->
							<?php echo FormHelper::getRelationRow($parent, 'parents', $this->form); ?>							
							<!-- End: Row for one existing relation -->
														
				<?php   
							$k = 7 - $k;			
		  				}
		  			}
				}
				?>
				<!-- End: List of existing relations -->
			</tbody>	
			<!-- End: tabel body for relations -->
		</table>
		<!-- End: Relations -->
	
		<!-- Save + cancel buttons -->
		<?php echo FormHelper::getButtons(2) ;?>							
		<!-- End save + cancel buttons -->		
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