<?php

defined('TYPO3_MODE') or die();

$fieldLanguageFilePrefix = 'LLL:EXT:geolocations/Resources/Private/Language/locallang_db.xlf:';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToInsertRecords('tx_geolocations_domain_model_category');

$tx_geolocations_domain_model_category = [
    'ctrl' => [
        'title' => $fieldLanguageFilePrefix . 'tx_geolocations_domain_model_category',
        'label' => 'title',
        'prependAtCopy' => true,
        'hideAtCopy' => true,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'dividers2tabs' => true,
        'default_sortby' => 'title',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'iconfile' => 'EXT:geolocations/Resources/Public/Icons/tx_geolocations_domain_model_category.svg',
        'searchFields' => 'uid,title',
    ],
    'interface' => [
        'showRecordFieldList' => 'uid,pid,tstamp,crdate,cruser_id,sys_language_uid,l10n_parent,l10n_diffsource,deleted,hidden,title,description,image,marker'
    ],
    'columns' => [
        'pid' => [
            'label' => 'pid',
            'config' => [
                'type' => 'passthrough'
            ]
        ],
        'tstamp' => [
            'label' => 'tstamp',
            'config' => [
                'type' => 'passthrough',
            ]
        ],
        'crdate' => [
            'label' => 'crdate',
            'config' => [
                'type' => 'passthrough',
            ]
        ],
        'cruser_id' => [
            'label' => 'cruser_id',
            'config' => [
                'type' => 'passthrough'
            ]
        ],
        'sys_language_uid' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'special' => 'languages',
                'items' => [
                    [
                        'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.allLanguages',
                        -1,
                        'flags-multiple'
                    ],
                ],
                'default' => 0,
            ]
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_geolocations_domain_model_category',
                'foreign_table_where' => 'AND tx_geolocations_domain_model_category.pid=###CURRENT_PID### AND tx_geolocations_domain_model_category.sys_language_uid IN (-1,0)',
            ]
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
                'default' => ''
            ]
        ],
        'hidden' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
                'default' => 0
            ]
        ],
        'title' => [
            'exclude' => 0,
            'label' => $fieldLanguageFilePrefix . 'tx_geolocations_domain_model_category.title',
            'config' => [
                'type' => 'input',
                'size' => 60,
                'eval' => 'required',
            ]
        ],
        'description' => [
            'exclude' => 1,
            'label' => $fieldLanguageFilePrefix . 'tx_geolocations_domain_model_category.description',
            'config' => [
                'type' => 'text',
                'cols' => 60,
                'rows' => 5,
            ]
        ],
        'image' => [
            'label' => $fieldLanguageFilePrefix . 'tx_geolocations_domain_model_location.image',
            'exclude' => true,
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
                'image',
                [
                'appearance' => [
                    'createNewRelationLinkTitle' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:images.addFileReference',
                    'headerThumbnail' => [
                        'width' => '25',
                        'height' => '25c'
                    ]
                ],
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ],
                'foreign_types' => [
                    \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => ['showitem' => '--palette--;;imageoverlayPalette,--palette--;;filePalette']]
                ], $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
            )
        ],
        'marker' => [
            'label' => $fieldLanguageFilePrefix . 'tx_geolocations_domain_model_category.marker',
            'exclude' => true,
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
                'marker',
                [
                'maxitems' => 1,
                'appearance' => [
                    'createNewRelationLinkTitle' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:images.addFileReference',
                    'headerThumbnail' => [
                        'width' => '16',
                        'height' => '16'
                    ]
                ],
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ],
                'foreign_types' => [
                    \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => ['showitem' => '--palette--;;filePalette']]
                ], 'gif,png,svg'
            )
        ]
    ],
    'types' => [
        '1' => ['showitem' => 'hidden,l10n_parent,l10n_diffsource,sys_language_uid,title,description,image,marker'],
    ]
];

return $tx_geolocations_domain_model_category;
