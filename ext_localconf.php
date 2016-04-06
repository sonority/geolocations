<?php

defined('TYPO3_MODE') or die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:' . $_EXTKEY . '/Configuration/TsConfig/Page.txt">');

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Sonority.' . $_EXTKEY, 'Pi1',
    [
        'Location' => 'map,list,search',
        'Ajax' => 'search'
    ],
    [
        'Ajax' => 'search'
    ]
);

// Register eID for ajax action-call
$GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include']['geolocations'] = 'EXT:geolocations/Classes/Eid/AjaxBootstrap.php';

// Custom cache, done with the caching framework
if (!is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['cache_geolocations_category'])) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['cache_geolocations_category'] = [];
}

// Define state cache, if not already defined
if (!is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['cache_geolocations_address'])) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['cache_geolocations_address'] = [
        'frontend' => 'TYPO3\\CMS\\Core\\Cache\\Frontend\\VariableFrontend',
        'backend' => 'TYPO3\\CMS\\Core\\Cache\\Backend\\Typo3DatabaseBackend',
    ];
}
