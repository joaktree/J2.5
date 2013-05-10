<?php
defined('_JEXEC') or die('Restricted access');
?>

<form action="index.php?option=com_joaktree" method="post" id="adminForm" name="adminForm">
<?php echo JHTML::_( 'form.token' ); ?>

<table class="adminform">
	<tr>
		<td width="30%" valign="top">
			<div id="cpanel">
				
				<div style="float: left;">
					<div class="icon">
						<a href="index.php?option=com_joaktree&view=jt_applications">
							<img src="components/com_joaktree/assets/images/icon-48-app.png" />
							<span><?php echo JText::_('JT_SUBMENU_APPLICATIONS'); ?></span>
						</a>
					</div>
				</div>

				<div style="float: left;">
					<div class="icon">
						<a href="index.php?option=com_joaktree&view=jt_familytree">
							<img src="components/com_joaktree/assets/images/icon-48-familytree.png" />
							<span><?php echo JText::_('JT_SUBMENU_FAMILYTREES'); ?></span>
						</a>
					</div>
				</div>
				
				<div style="float: left;">
					<div class="icon">
						<a href="index.php?option=com_joaktree&view=jt_maps">
							<img src="components/com_joaktree/assets/images/icon-48-map.png" />
							<span><?php echo JText::_('JT_SUBMENU_MAPS'); ?></span>
						</a>
					</div>
				</div>

				<div style="float: left;">
					<div class="icon">
						<a href="index.php?option=com_joaktree&view=jt_admin">
							<img src="components/com_joaktree/assets/images/icon-48-person.png" />
							<span><?php echo JText::_('JT_SUBMENU_PERSONS'); ?></span>
						</a>
					</div>
				</div>

				<div style="float: left;">
					<div class="icon">
						<a href="index.php?option=com_joaktree&view=jt_settings&layout=personname">
							<img src="components/com_joaktree/assets/images/icon-48-display2.png" />
							<span><?php echo JText::_('JT_SUBMENU_PERSON_NAMEDISPLAY'); ?></span>
						</a>
					</div>
				</div>

				<div style="float: left;">
					<div class="icon">
						<a href="index.php?option=com_joaktree&view=jt_settings&layout=personevent">
							<img src="components/com_joaktree/assets/images/icon-48-display1.png" />
							<span><?php echo JText::_('JT_SUBMENU_PERSON_EVENTDISPLAY'); ?></span>
						</a>
					</div>
				</div>

				<div style="float: left;">
					<div class="icon">
						<a href="index.php?option=com_joaktree&view=jt_settings&layout=relationevent">
							<img src="components/com_joaktree/assets/images/icon-48-display3.png" />
							<span><?php echo JText::_('JT_SUBMENU_RELATION_EVENTDISPLAY'); ?></span>
						</a>
					</div>
				</div>

				<div style="float: left;">
					<div class="icon">
						<a href="index.php?option=com_joaktree&view=jt_themes">
							<img src="components/com_joaktree/assets/images/icon-48-theme.png" />
							<span><?php echo JText::_('JT_SUBMENU_THEMES'); ?></span>
						</a>
					</div>
				</div>
				
				<div style="float: left;">
					<div class="icon">
						<a href="http://joaktree.com/download/JoaktreeManuelv<?php echo $this->lists['version']; ?>.pdf" target="_manual">
							<img src="components/com_joaktree/assets/images/icon-48-help.png" />
							<span><?php echo JText::_('JHELP'); ?></span>
						</a>
					</div>
				</div>
			</div>
		</td>
		<td width="60%" valign="top">
			<div style="300px;border:1px solid #ccc;background:#fff;margin:15px;padding:15px">
				<div style="float: right; margin:10px;">
					<img src="components/com_joaktree/assets/images/logo-80-joaktree.png" alt="logo-joaktree" />
				</div>
				<h1 style="margin: 0;"><?php echo JText::_('COM_JOAKTREE'); ?></h1>
				<h2><?php echo JText::_('COM_JOAKTREE_DESC'); ?></h2>
				<p><span class="jt-default"><?php echo JText::_('JT_VERSION');?>:</span><?php echo $this->lists['version'] ;?></p>
				<p><span class="jt-default"><?php echo JText::_('JT_COPYRIGHT');?>:</span>
					&copy; 2009 - <?php echo date("Y"); ?> Niels van Dantzig
					&nbsp;(<a href="http://joaktree.com/" target="_joaktree">joaktree.com</a>)
				</p>
				<div style="clear:both">&nbsp;</div>
				
				<h3><?php echo JText::_('COM_JOAKTREE_LICENSES');?></h3>
				<p><span class="jt-default"><?php echo JText::_('COM_JOAKTREE'); ?></span>
					<a href="http://www.gnu.org/licenses/gpl.html" target="_blank"><abbr title="GNU General Public License">GPL</abbr></a>
				</p>
				
				<p style="font-size: 110%; color: red;"><?php echo JText::_('MBJ_DISCAIMER'); ?></p>
				
				<?php foreach ($this->lists['providers'] as $provider) { ?>
					<p><span class="jt-default"><?php echo $provider->name; ?></span>
						<a href="<?php echo $provider->license; ?>" target="_viewlicense"><?php echo $provider->license; ?></a>
				
					</p>
				<?php } ?>
				
				<div style="clear:both">&nbsp;</div>
				<h3><?php echo JText::_('COM_JOAKTREE_CREDITS');?></h3>
				<p><strong><?php echo JText::_('Character set conversion');?></strong></p>
				<p class="license"><?php echo JText::_('ANSEL to UNICODE');?></p>
				<p>The routine for conversion of ANSEL character set to UNICODE is based on the routine of <a href="http://solventus.so.funpic.de" target="_blank">JGen_0.9.80 by Solventus</a></p>
				<p>This ANSEL to UNICODE conversion is based on the table by <a href="http://www.heiner-eichmann.de" target="_blank">Heiner Eichmann (http://www.heiner-eichmann.de)</a></p>
				<p>&nbsp;</p>
	
				<p class="license"><?php echo JText::_('UNICODE to UTF-8');?></p>
				<p>The routine for conversion of UNICODE character set to UTF-8 is based on the routine of <a href="http://hsivonen.iki.fi/php-utf8/" target="_blank">Henri Sivonen</a></p>
				<p>The code has been adapted from the <a href="http://lxr.mozilla.org/seamonkey/source/intl/uconv/src/nsUTF8ToUnicode.cpp" target="_blank">UTF-8 to UTF-16</a> 
				   and <a href="http://lxr.mozilla.org/seamonkey/source/intl/uconv/src/nsUnicodeToUTF8.cpp" target="_blank">UTF-16 to UTF-8</a> 
				   converters of Mozilla. 
				   Hence, the code is provided under an <a href="http://www.mozilla.org/MPL/NPL-1.1.html" target="_blank"><abbr title="Netscape Public License">NPL</abbr> 1.1</a>/
				   <a href="http://www.gnu.org/licenses/gpl.html" target="_blank"><abbr title="GNU General Public License">GPL</abbr></a> 2.0/
				   <a href="http://www.gnu.org/licenses/lgpl.html" target="_blank"><abbr title="GNU Lesser General Public License">LGPL</abbr></a> 2.1 tri-license.
				</p>
				<p>&nbsp;</p>
	
				<p><strong><?php echo JText::_('Pictures');?></strong></p>
				<p>The images are shown using routine from ImageSlideShow by <a href="http://briask.com/blog/download-imageslideshow/" target="_blank">Briask</a>.
				</p> 
				<p><a href="http://www.shadowbox-js.com/index.html" target="_blank">Shadowbox.js</a> is used for viewing as well.
				</p>
				<p>The ImageSlideShow is provide under a <a href="http://www.gnu.org/licenses/gpl.html" target="_blank"><abbr title="GNU General Public License">GPL</abbr></a> licence.
				</p>
			</div>
		</td>
	</tr>
</table>

<input type="hidden" name="option" value="com_joaktree" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="joaktree" />


</form>