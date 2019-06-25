<?php

defined('TYPO3_MODE') or die();

$fieldLanguageFilePrefix = 'LLL:EXT:geolocations/Resources/Private/Language/locallang_db.xlf:';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToInsertRecords('tx_geolocations_domain_model_location');

$extensionConfiguration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['geolocations']);

$tx_geolocations_domain_model_location = [
    'ctrl' => [
        'title' => $fieldLanguageFilePrefix . 'tx_geolocations_domain_model_location',
        'label' => 'title',
        'label_alt' => 'address,bodytext',
        'hideAtCopy' => true,
        'prependAtCopy' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.prependAtCopy',
        'copyAfterDuplFields' => 'sys_language_uid',
        'useColumnsForDefaultValues' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'languageField' => 'sys_language_uid',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => true,
        'default_sortby' => 'title',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
            'fe_group' => 'fe_group'
        ],
        'iconfile' => 'EXT:geolocations/Resources/Public/Icons/tx_geolocations_domain_model_location.svg',
        'searchFields' => 'uid,title,address,zip,city',
        'thumbnail' => 'image'
    ],
    'interface' => [
        'showRecordFieldList' => 'pid,tstamp,crdate,cruser_id,sys_language_uid,l10n_parent,l10n_diffsource,deleted,hidden,starttime,endtime,fe_group,title,bodytext,image,marker,latitude,longitude,place_id,address,zip,city,zone,country,www,email,phone,status,datetime,fe_user,categories'
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
                'type' => 'passthrough'
            ]
        ],
        'crdate' => [
            'label' => 'crdate',
            'config' => [
                'type' => 'passthrough'
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
                    ]
                ],
                'default' => 0
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
                    ['', 0]
                ],
                'foreign_table' => 'tx_geolocations_domain_model_location',
                'foreign_table_where' => 'AND tx_geolocations_domain_model_location.pid=###CURRENT_PID### AND tx_geolocations_domain_model_location.sys_language_uid IN (-1,0)'
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
                'default' => 1
            ]
        ],
        'starttime' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:starttime_formlabel',
            'config' => [
                'type' => 'input',
                'size' => 16,
                'eval' => 'datetime',
                'renderType' => 'inputDateTime',
                'default' => 0,
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ],
            ]
        ],
        'endtime' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:endtime_formlabel',
            'config' => [
                'type' => 'input',
                'size' => 16,
                'eval' => 'datetime',
                'renderType' => 'inputDateTime',
                'default' => 0,
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ],
            ]
        ],
        'fe_group' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.fe_group',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'size' => 5,
                'maxitems' => 20,
                'items' => [
                    [
                        'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.hide_at_login',
                        -1
                    ],
                    [
                        'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.any_login',
                        -2
                    ],
                    [
                        'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.usergroups',
                        '--div--'
                    ],
                ],
                'exclusiveKeys' => '-1,-2',
                'foreign_table' => 'fe_groups',
                'foreign_table_where' => 'ORDER BY fe_groups.title'
            ]
        ],
        'title' => [
            'exclude' => 0,
            'label' => $fieldLanguageFilePrefix . 'tx_geolocations_domain_model_location.title',
            'config' => [
                'type' => 'input',
                'size' => 60,
                'eval' => 'required'
            ]
        ],
        'bodytext' => [
            'exclude' => 0,
            'l10n_mode' => 'prefixLangTitle',
            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:bodytext_formlabel',
            'config' => [
                'type' => 'text',
                'cols' => 80,
                'rows' => 10,
                //'softref' => 'rtehtmlarea_images,typolink_tag,images,email[subst],url',
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
                    'overrideChildTca' => [
                        'types' => [
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => ['showitem' => '--palette--;;imageoverlayPalette,--palette--;;filePalette']
                        ]
                    ]
                ],
                $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
            )
        ],
        'marker' => [
            'label' => $fieldLanguageFilePrefix . 'tx_geolocations_domain_model_location.marker',
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
                    'overrideChildTca' => [
                        'types' => [
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => ['showitem' => '--palette--;;filePalette']
                        ]
                    ]
                ],
                'gif,png,svg'
            )
        ],
        'latitude' => [
            'exclude' => 1,
            'label' => $fieldLanguageFilePrefix . 'tx_geolocations_domain_model_location.latitude',
            'config' => [
                'type' => 'input',
                'size' => 14,
                'eval' => 'trim',
                'placeholder' => '00.00000000'
            ]
        ],
        'longitude' => [
            'exclude' => 1,
            'label' => $fieldLanguageFilePrefix . 'tx_geolocations_domain_model_location.longitude',
            'config' => [
                'type' => 'input',
                'size' => 14,
                'eval' => 'trim',
                'placeholder' => '00.00000000'
            ]
        ],
        'place_id' => [
            'exclude' => 1,
            'label' => $fieldLanguageFilePrefix . 'tx_geolocations_domain_model_location.placeId',
            'config' => [
                'type' => 'input',
                'size' => 17,
                'eval' => 'trim'
            ]
        ],
        'address' => [
            'exclude' => 1,
            'label' => $fieldLanguageFilePrefix . 'tx_geolocations_domain_model_location.address',
            'config' => [
                'type' => 'user',
                'userFunc' => 'Sonority\Geolocations\Form\Element\GeoCodeElement->render',
                'size' => 30,
                'eval' => 'required,trim'
            ]
        ],
        'zip' => [
            'exclude' => 1,
            'label' => $fieldLanguageFilePrefix . 'tx_geolocations_domain_model_location.zip',
            'config' => [
                'type' => 'input',
                'size' => 8,
                'max' => 8,
                'eval' => 'num,trim',
                'placeholder' => '1234'
            ]
        ],
        'city' => [
            'exclude' => 1,
            'label' => $fieldLanguageFilePrefix . 'tx_geolocations_domain_model_location.city',
            'config' => [
                'type' => 'input',
                'size' => 20,
                'eval' => 'trim'
            ]
        ],
        'zone' => [
            'exclude' => 1,
            'label' => $fieldLanguageFilePrefix . 'tx_geolocations_domain_model_location.zone',
            'config' => [
                'type' => 'select',
                'items' => [
                    ['', ''],
                ],
                'foreign_table' => 'static_country_zones',
                'foreign_table_where' => ' ORDER BY static_country_zones.zn_name_local',
                'itemsProcFunc' => 'SJBR\StaticInfoTables\Hook\Backend\Form\ElementRenderingHelper->translateCountryZonesSelector',
                'disableNoMatchingValueElement' => true,
                'renderType' => 'selectSingle',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1
            ]
        ],
        'country' => [
            'exclude' => 1,
            'label' => $fieldLanguageFilePrefix . 'tx_geolocations_domain_model_location.country',
            'config' => [
                'type' => 'select',
                'foreign_table' => 'static_countries',
                'foreign_table_where' => 'ORDER BY static_countries.cn_short_en',
                'itemsProcFunc' => 'SJBR\StaticInfoTables\Hook\Backend\Form\ElementRenderingHelper->translateCountriesSelector',
                'renderType' => 'selectSingle',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
                'eval' => 'required'
            ]
        ],
        'www' => [
            'exclude' => 1,
            'label' => $fieldLanguageFilePrefix . 'tx_geolocations_domain_model_location.www',
            'config' => [
                'type' => 'input',
                'size' => 30,
                //'softref' => 'typolink',
                'eval' => 'nospace,trim,domainname',
                'placeholder' => 'https://www.domainname.com/'
            ]
        ],
        'email' => [
            'exclude' => 1,
            'label' => $fieldLanguageFilePrefix . 'tx_geolocations_domain_model_location.email',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'nospace,lower,email',
                'placeholder' => 'email@domainname.com'
            ]
        ],
        'phone' => [
            'exclude' => 1,
            'label' => $fieldLanguageFilePrefix . 'tx_geolocations_domain_model_location.phone',
            'config' => [
                'type' => 'input',
                'size' => 20,
                'eval' => 'trim',
                'placeholder' => '+43 1 12345678'
            ]
        ],
        'status' => [
            'exclude' => 1,
            'label' => $fieldLanguageFilePrefix . 'tx_geolocations_domain_model_location.status',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
                ],
                'size' => 1,
                'maxitems' => 1
            ]
        ],
        'datetime' => [
            'exclude' => 1,
            'label' => $fieldLanguageFilePrefix . 'tx_geolocations_domain_model_location.datetime',
            'config' => [
                'type' => 'input',
                'size' => 16,
                'eval' => 'date',
                'renderType' => 'inputDateTime'
            ]
        ],
        'fe_user' => [
            'exclude' => 1,
            'label' => $fieldLanguageFilePrefix . 'tx_geolocations_domain_model_location.feUser',
            'config' => [
                'type' => 'select',
                'multiple' => true,
                'foreign_table' => 'fe_users',
                'foreign_table_where' => 'AND fe_users.usergroup IN (###PAGE_TSCONFIG_ID###) ORDER BY fe_users.username',
                'enableMultiSelectFilterTextfield' => true,
                'renderType' => 'selectMultipleSideBySide',
                'size' => 5,
                'minitems' => 0,
                'maxitems' => 999
            ]
        ],
        'categories' => [
            'exclude' => 1,
            'label' => $fieldLanguageFilePrefix . 'tx_geolocations_domain_model_location.categories',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'MM' => 'tx_geolocations_location_category_mm',
                'foreign_table' => 'tx_geolocations_domain_model_category',
                'foreign_table_field' => 'tablenames',
                'foreign_sortby' => 'sorting_foreign',
                /* Feature #81976: https://forge.typo3.org/issues/81976 */
                /*
                'fieldControl' => [
                    'addRecord' => [
                        'disabled' => false,
                        'options' => [
                            'pid' => '###CURRENT_PID###',
                            'setValue' => 'prepend',
                            'table' => 'tx_mymap_domain_model_category',
                            'title' => 'LLL:EXT:lang/locallang_misc.xlf:shortcut_create'
                        ]
                    ],
                    'editPopup' => [
                        'disabled' => false,
                        'options' => [
                            'title' => 'LLL:EXT:lang/locallang_misc.xlf:shortcut_edit',
                            'windowOpenParameters' => 'height=350,width=580,status=0,menubar=0,scrollbars=1'
                        ]
                    ]
                ]
                */
            ]
        ]
    ],
    'types' => [
        '1' => [
            'columnsOverrides' => [
                'bodytext' => [
                    'config' => [
                        'enableRichtext' => 1,
                        'fieldControl' => [
                            'fullScreenRichtext' => [
                                'disabled' => false,
                                'options' => [
                                    'title' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:bodytext.W.RTE'
                                ]
                            ]
                        ],
                        'richtextConfiguration' => 'default'
                    ]
                ]
            ],
            'showitem' => '
                l10n_parent,l10n_diffsource,hidden,sys_language_uid,
                title,bodytext,
                --palette--;;paletteAddress,
                --palette--;;paletteGeolocation,
                --palette--;;paletteZone,www,email,phone,
                --palette--;;paletteStatus,fe_user,categories,
                --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.extended,
                    --palette--;;paletteMedia,
                --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access,
                    --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.access;paletteAccess,'
        ]
    ],
    'palettes' => [
        'paletteAddress' => [
            'showitem' => 'address,zip,city',
            'canNotCollapse' => true
        ],
        'paletteGeolocation' => [
            'showitem' => 'latitude,longitude,place_id',
            'canNotCollapse' => false
        ],
        'paletteZone' => [
            'showitem' => 'zone,country',
            'canNotCollapse' => false
        ],
        'paletteStatus' => [
            'showitem' => 'status,datetime',
            'canNotCollapse' => true
        ],
        'paletteMedia' => [
            'showitem' => 'image,marker',
            'canNotCollapse' => true
        ],
        'paletteAccess' => [
            'showitem' => 'starttime;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:starttime_formlabel,
                    endtime;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:endtime_formlabel,
                    --linebreak--, fe_group;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:fe_group_formlabel',
            'canNotCollapse' => true
        ]
    ]
];
$TCA['fe_users'] = [
    'ctrl' => [
        'label' => 'name'
    ]
];

// Display zones of the selected country
if (intval($extensionConfiguration['zonesByCountry'])) {
    $tx_geolocations_domain_model_location['columns']['country']['onChange'] = 'reload';
    $tx_geolocations_domain_model_location['columns']['zone']['config']['foreign_table_where'] = 'AND zn_country_uid=\'###REC_FIELD_country###\' ORDER BY static_country_zones.zn_name_local';
}

return $tx_geolocations_domain_model_location;
