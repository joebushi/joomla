<?php
foreach($this->items->children() as $item)
{
	if($item->name() == 'action')
	{
		echo $item->attributes('name');
	}
}
?>