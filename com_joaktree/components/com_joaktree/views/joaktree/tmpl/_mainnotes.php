<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php
$html = '';

$lines = $this->person->getNotes($this->notes[ 'subtype' ], $this->notes[ 'orderNumber' ], $this->notes[ 'relation_id' ]);

$html .= '<div class="jt-note">';
$html .= '<div class="jt-h3">';
$html .= '<span style="float: left;">'.JText::_('JT_NOTES').' '.JText::_('JT_FOR').' '.$this->person->firstName.' '.$this->person->familyName.'</span>';

if ($this->lists['technology'] != 'b') {
	// box can be closed
	$html .= '<a href="#" style="float: right;" title="'.JText::_('JT_CLOSE').'" ';
	$html .= 'onclick="toggleNotesSources(0, \'jt1notesid\', \'jt2notesid\', \'jt1sourcesid\', \'jt2sourcesid\');return false;">';
	$html .= '<strong>x</strong>';
	$html .= '</a>';
} 
$html .= '</div>';
$html .= '<div class="jt-clearfix"></div>';


// loop through notes
$i=0;
$n=count( $lines );

foreach ($lines as $line) {
	// if more than 1 note, a number is shown
	if ($n > 1) {
		$i++;
		$html .= $i.'.&nbsp;';
	}
	
	$rows = (int) floor(strlen($line->text)/80);
	$html .= '<textarea rows="'.$rows.'" cols="98">' .$line->text . '</textarea>';			
} // end of loop

$html .= '</div>';

echo $html;

?>


