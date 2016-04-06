<?php

namespace Sonority\Geolocations\Eid;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Core\Bootstrap;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Frontend\Utility\EidUtility;

/**
 * This class is called via eID
 *
 * @package geolocations
 */
class AjaxBootstrap
{

    /**
     * @var \array
     */
    protected $configuration;

    /**
     * @var \array
     */
    protected $bootstrap;

    /**
     * The main Method
     *
     * @return \string
     */
    public function run()
    {
        return $this->bootstrap->run('', $this->configuration);
    }

    /**
     * Initialize Extbase
     *
     * @param array $TYPO3_CONF_VARS The global array. Will be set internally
     */
    public function __construct($TYPO3_CONF_VARS)
    {
        $_GP = GeneralUtility::_GPmerged('tx_geolocations_pi1');
        // We need controller, action and pid to continue
        if (!(!count($_GP) || empty($_GP['controller']) || empty($_GP['action']))) {
            // Set basic configuration
            $this->configuration = [
                'pluginName' => 'Pi1',
                'vendorName' => 'Sonority',
                'extensionName' => 'Geolocations',
                'controller' => $_GP['controller'],
                'action' => $_GP['action'],
                'mvc' => [
                    'requestHandlers' => [
                        'TYPO3\CMS\Extbase\Mvc\Web\FrontendRequestHandler' => 'TYPO3\CMS\Extbase\Mvc\Web\FrontendRequestHandler'
                    ]
                ],
                'settings' => [],
            ];
            // Create request and dispatch it
            $this->bootstrap = new Bootstrap();
            // Get FE-user object
            $feUserObj = EidUtility::initFeUser();
            // Get PID
            $pid = ($_GP['pid'] ? intval($_GP['pid']) : 1);
            // Initialize TypoScript based frontend
            $GLOBALS['TSFE'] = GeneralUtility::makeInstance(
                    TypoScriptFrontendController::class, $TYPO3_CONF_VARS, $pid, 0, true
            );
            // Connect to SQL database
            $GLOBALS['TSFE']->connectToDB();
            // Set FE-user object
            $GLOBALS['TSFE']->fe_user = $feUserObj;
            // Set PID
            $GLOBALS['TSFE']->id = $pid;
            // Determine the id and evaluate preview settings
            $GLOBALS['TSFE']->determineId();
            // Initialize the TypoScript template parser
            $GLOBALS['TSFE']->initTemplate();
            // Initialize the config-array
            $GLOBALS['TSFE']->getConfigArray();
            // Start the rendering of the TypoScript template structure
            $GLOBALS['TSFE']->newCObj();
        } else {
            die();
        }
    }

}

// Make instance of bootstrap and run
$Eid = GeneralUtility::makeInstance(AjaxBootstrap::class, $GLOBALS['TYPO3_CONF_VARS']);
echo $Eid->run();
