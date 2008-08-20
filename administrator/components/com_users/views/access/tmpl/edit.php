<?php 
$extensions = $this->access->getExtensions();
foreach($extensions as $extension)
{
	echo '<fieldset><legend>'.$extension.'</legend>';
	echo $this->access->getDescription($extension);
	echo $this->access->render(JRequest::getInt('id'), $extension, 'action');
	echo $this->access->render(JRequest::getInt('id'), $extension, 'content');
	echo '</fieldset>';
}
