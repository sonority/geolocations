<?php

defined('TYPO3_MODE') or die();

// Load extension manager configuration
if (!empty($_EXTCONF)) {
    $_EXTCONF = unserialize($_EXTCONF);
}

// CSH - context sensitive help
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
    'tt_content.pi_flexform.' . $_EXTKEY . '_pi1.list',
    'EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_csh_flexforms.xlf'
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'Sonority.' . $_EXTKEY, 'pi1', 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_be.xlf:pi1_title'
);

if (!empty($_EXTCONF['enableBackendModule'])) {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'Sonority.' . $_EXTKEY, 'web', 'mod1', '',
        [
            'Administration' => 'overview,geocode,settingsError,startGeocodeProcess,checkGeocodeProcess',
        ],
        [
            'access' => 'user,group',
            'icon' => 'EXT:' . $_EXTKEY . '/Resources/Public/Icons/module-tstemplate.svg',
            'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_mod.xlf'
        ]
    );
}

// Add static typoscript configurations
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript/', 'Geolocations');
