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
            'Administration' => 'overview,fillMissingCoordinates,geocodeAddress,settingsError,ajaxGeocodeAddress,ajaxStartGeocodeProcess,ajaxCheckGeocodeProcess',
        ], [
            'access' => 'user,group',
            'icon' => 'EXT:' . $_EXTKEY . '/Resources/Public/Icons/module-tstemplate.svg',
            'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_mod.xlf'
        ]
    );
}

// Add static typoscript configurations
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript/', 'Geolocations');

if (TYPO3_MODE === 'BE') {
    /** @var \TYPO3\CMS\Core\Imaging\IconRegistry $iconRegistry */
    $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
    $iconRegistry->registerIcon(
        'apps-pagetree-folder-contains-locations',
        \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        [
            'source' => 'EXT:geolocations/Resources/Public/Icons/ext-geolocations-folder-tree.svg'
        ]
    );
    $iconRegistry->registerIcon(
        'ext-geolocations-wizard-icon', \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        [
            'source' => 'EXT:geolocations/Resources/Public/Icons/ce_wizard.svg'
        ]
    );
    // Override geolocations icon
    $GLOBALS['TCA']['pages']['ctrl']['typeicon_classes']['contains-locations'] = 'apps-pagetree-folder-contains-locations';
    $GLOBALS['TCA']['pages']['columns']['module']['config']['items'][] = [
        0 => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_be.xlf:locations-folder',
        1 => 'locations',
        2 => 'apps-pagetree-folder-contains-locations'
    ];
}