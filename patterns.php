<?php

define('Y', $this->type);			//	Y - YOU
define('E', $this->enemy_type);		//	E - ENEMY

$patterns = array(
	array(
		array(
			1 => array(1 => '', '', ''),
				array(1 => '', '', ''),
				array(1 => '', '', '')
		),
		array(
			1 => array(1 => '', '', ''),
				array(1 => '', Y, ''),
				array(1 => '', '', '')
		),
		array(
			1 => array(1 => '', '', ''),
				array(1 => '', Y, E),
				array(1 => '', '', '')
		),
		array(
			1 => array(1 => Y, '', ''),
				array(1 => '', Y, E),
				array(1 => '', '', '')
		),
		array(
			1 => array(1 => Y, '', ''),
				array(1 => '', Y, E),
				array(1 => '', '', E)
		),
		array(
			1 => array(1 => Y, '', Y),
				array(1 => '', Y, E),
				array(1 => '', '', E)
		)
	),
	array(
		1 => array(
			1 => array(1 => '', '', ''),
				array(1 => '', E, ''),
				array(1 => '', '', '')
		),
		array(
			1 => array(1 => '', '', ''),
				array(1 => '', E, ''),
				array(1 => '', '', Y)
		),
		array(
			1 => array(1 => E, '', ''),
				array(1 => '', E, ''),
				array(1 => '', '', Y)
		),
		array(
			1 => array(1 => E, '', ''),
				array(1 => '', E, ''),
				array(1 => Y, '', Y)
		)
	),
	array(
		2 => array(
			1 => array(1 => '', '', ''),
				array(1 => '', Y, ''),
				array(1 => '', '', E)
		),
		array(
			1 => array(1 => Y, '', ''),
				array(1 => '', Y, ''),
				array(1 => '', '', E)
		)
	),
	array(
		1 => array(
			1 => array(1 => '', '', ''),
				array(1 => '', '', ''),
				array(1 => '', E, '')
		),
		array(
			1 => array(1 => '', '', ''),
				array(1 => '', '', ''),
				array(1 => '', E, Y)
		),
		array(
			1 => array(1 => '', '', ''),
				array(1 => E, '', ''),
				array(1 => '', E, Y)
		),
		array(
			1 => array(1 => '', Y, ''),
				array(1 => E, '', ''),
				array(1 => '', E, Y)
		)
	),
	array(
		array(
			1 => array(1 => '', '', ''),
				array(1 => '', '', ''),
				array(1 => '', '', '')
		),
		array(
			1 => array(1 => '', '', ''),
				array(1 => '', '', ''),
				array(1 => '', Y, '')
		),
		array(
			1 => array(1 => '', '', ''),
				array(1 => '', '', ''),
				array(1 => E, Y, '')
		),
		array(
			1 => array(1 => '', '', ''),
				array(1 => '', '', Y),
				array(1 => E, Y, '')
		),
		array(
			1 => array(1 => E, '', ''),
				array(1 => '', '', Y),
				array(1 => E, Y, '')
		),
		array(
			1 => array(1 => E, '', ''),
				array(1 => '', '', Y),
				array(1 => E, Y, Y)
		)
	),
	array(
		1 => array(
			1 => array(1 => E, '', ''),
				array(1 => '', '', ''),
				array(1 => '', '', '')
		),
		array(
			1 => array(1 => E, '', ''),
				array(1 => '', Y, ''),
				array(1 => '', '', '')
		)
	)
);
shuffle($patterns);
 ?>