<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "geolocations".
 *
 * Auto generated 06-04-2016 20:13
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = [
	'title' => 'Geolocations',
	'description' => 'AJAX-Search for locations (radialsearch & fulltextsearch) and display them on a google-map with your own styles.',
	'category' => 'fe',
	'version' => '0.0.2',
	'state' => 'alpha',
	'uploadfolder' => false,
	'createDirs' => '',
	'clearcacheonload' => true,
	'author' => 'Stephan Kellermayr',
	'author_email' => 'stephan.kellermayr@gmail.com',
	'author_company' => 'sonority.at - MULTIMEDIA ART DESIGN',
	'constraints' => [
		'depends' => [
			'typo3' => '7.4.0-7.6.99',
            'extbase' => '',
            'fluid' => '',
            'static_info_tables' => ''
		],
		'conflicts' => [],
		'suggests' => [],
	],
];
