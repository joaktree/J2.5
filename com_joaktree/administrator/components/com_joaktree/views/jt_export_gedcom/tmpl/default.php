<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php echo JHTML::_( 'form.token' ); ?>
<?php JHtml::_('behavior.mootools'); ?>

<!-- mootools -->
<script>
	window.addEvent('domready', function() {
		exportGedcom();
	});
</script>

<div id="cpanel">
	<div id="head_process" style="display: inline;">
		<div class="fltlft" style="height: 114px;">
			<h1><?php echo JText::_('JTPROCGEDCOM_PROC'); ?></h1>
			<?php echo JText::_('JTPROCGEDCOM_PROC_TXT'); ?>
		</div>
		<div class="fltrt">
			<div class="icon">
				<img src="components/com_joaktree/assets/images/ajax-loader.gif" />
				<span><?php echo JText::_('JT_LOADING'); ?></span>
			</div>
		</div>
	</div>
	<div id="head_finished" style="display: none;">
		<div class="fltlft">
			<h1><?php echo JText::_('JTPROCGEDCOM_HFINISHED'); ?></h1>
		</div>
		<div class="fltrt">
			<div class="icon">
				<a href="index.php?option=com_joaktree&view=jt_applications">
					<img src="components/com_joaktree/assets/images/icon-48-app.png" />
					<span><?php echo JText::_('JT_SUBMENU_APPLICATIONS'); ?></span>
				</a>
			</div>
			<div class="icon">
				<a href="index.php?option=com_joaktree&view=jt_familytree">
					<img src="components/com_joaktree/assets/images/icon-48-familytree.png" />
					<span><?php echo JText::_('JT_SUBMENU_FAMILYTREES'); ?></span>
				</a>
			</div>
			<div class="icon">
				<a href="index.php?option=com_joaktree&view=jt_admin">
					<img src="components/com_joaktree/assets/images/icon-48-person.png" />
					<span><?php echo JText::_('JT_SUBMENU_PERSONS'); ?></span>
				</a>
			</div>
		</div>
	</div>
	<div id="head_error" style="display: none;">
		<div class="fltlft">
			<h1><?php echo JText::_('JTPROCGEDCOM_HERROR'); ?></h1>
		</div>
		<div class="fltrt">
			<div class="icon">
				<a href="index.php?option=com_joaktree&view=jt_applications">
					<img src="components/com_joaktree/assets/images/icon-48-app.png" />
					<span><?php echo JText::_('JT_SUBMENU_APPLICATIONS'); ?></span>
				</a>
			</div>
		</div>
	</div>
</div>

<div style="clear: both;"></div>

<div class="width-50 fltrt">
	<fieldset class="adminform">
		<legend><?php echo JText::_('JTPROCESS_MSG'); ?></legend>
		<div id="procmsg"></div>
		
	</fieldset>
</div>

<?php foreach($this->items as $item) { ?>

<div class="width-40">
	<fieldset class="adminform">
		<legend><?php echo $item->title; ?></legend>
		<ul class="adminformlist">

			<li>
				<label><?php echo JText::_('JT_HEADING_ID'); ?></label>
				<input 
					type="text" 
					id="id_<?php echo $item->id; ?>" 
					class="readonly"
					value="<?php echo $item->id; ?>"
				/>
			</li>

			<li>
				<label><?php echo JText::_('JTPROCESS_START'); ?></label>
				<input 
					type="text" 
					id="start_<?php echo $item->id; ?>" 
					class="readonly" 
					value=""
				/>
			</li>

			<li>
				<label><?php echo JText::_('JTPROCESS_CURRENT'); ?></label>
				<input 
					type="text" 
					id="current_<?php echo $item->id; ?>" 
					class="readonly" 
					value=""
				/>
			</li>

			<li id="l_persons_<?php echo $item->id; ?>" style="display: none;">
				<label><?php echo JText::sprintf('JTGEDCOM_MESSAGE_PERSONS', null); ?></label>
				<input 
					type="text" 
					id="persons_<?php echo $item->id; ?>" 
					class="readonly" 
					value="0"
				/>
			</li>

			<li id="l_families_<?php echo $item->id; ?>"style="display: none;">
				<label><?php echo JText::sprintf('JTGEDCOM_MESSAGE_FAMILIES', null); ?></label>
				<input 
					type="text" 
					id="families_<?php echo $item->id; ?>" 
					class="readonly" 
					value="0"
				/>
			</li>

			<li id="l_sources_<?php echo $item->id; ?>"style="display: none;">
				<label><?php echo JText::sprintf('JTGEDCOM_MESSAGE_SOURCES', null); ?></label>
				<input 
					type="text" 
					id="sources_<?php echo $item->id; ?>" 
					class="readonly" 
					value="0"
				/>
			</li>

			<li id="l_repos_<?php echo $item->id; ?>"style="display: none;">
				<label><?php echo JText::sprintf('JTGEDCOM_MESSAGE_REPOS', null); ?></label>
				<input 
					type="text" 
					id="repos_<?php echo $item->id; ?>" 
					class="readonly" 
					value="0"
				/>
			</li>

			<li id="l_notes_<?php echo $item->id; ?>"style="display: none;">
				<label><?php echo JText::sprintf('JTGEDCOM_MESSAGE_NOTES', null); ?></label>
				<input 
					type="text" 
					id="notes_<?php echo $item->id; ?>" 
					class="readonly" 
					value="0"
				/>
			</li>

			<li id="l_unknown_<?php echo $item->id; ?>"style="display: none;">
				<label><?php echo JText::sprintf('JTGEDCOM_MESSAGE_UNKNOWN', null); ?></label>
				<input 
					type="text" 
					id="unknown_<?php echo $item->id; ?>" 
					class="readonly" 
					value="0"
				/>
			</li>
			
			<li>
				<label><?php echo JText::_('JTPROCESS_END'); ?></label>
				<input 
					type="text" 
					id="end_<?php echo $item->id; ?>" 
					class="readonly" 
					value=""
				/>
			</li>
			
		</ul>
		<div class="clr"> </div>
	</fieldset>	
</div>

<?php } ?>


