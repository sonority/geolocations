<?php

namespace Sonority\Geolocations\Controller;

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

use Sonority\Geolocations\Utility\Page;

/**
 * LocationController
 *
 * @package geolocations
 */
class BaseController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     */
    protected $configurationManager;

    /** @var string */
    protected $extConf;

    /** @var array */
    protected $ignoredSettingsForOverride = ['demandClass'];

    /**
     * Initializes the view before invoking an action method.
     * Override this method to solve assign variables common for all actions
     * or prepare the view in another way before the action is called.
     *
     * @param \TYPO3\CMS\Extbase\Mvc\View\ViewInterface $view The view to be initialized
     * @return void
     */
    protected function initializeView(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface $view)
    {
        $view->assign('contentObjectData', $this->configurationManager->getContentObject()->data);
        parent::initializeView($view);
    }

    /**
     * Create the demand object which defines which records will get shown
     *
     * @param array $settings
     * @param string $actionName The name of the current action
     * @param string $class optional class which must be an instance of \Sonority\Geolocations\Domain\Model\Dto\LocationDemand
     * @return \Sonority\Geolocations\Domain\Model\Dto\LocationDemand
     */
    protected function createDemandObjectFromSettings($settings, $class = 'Sonority\\Geolocations\\Domain\\Model\\Dto\\LocationDemand')
    {
        $class = isset($settings['demandClass']) && !empty($settings['demandClass']) ? $settings['demandClass'] : $class;
        /* @var $demand \Sonority\Geolocations\Domain\Model\Dto\LocationDemand */
        $demand = $this->objectManager->get($class, $settings);
        if (!$demand instanceof \Sonority\Geolocations\Domain\Model\Dto\LocationDemand) {
            throw new \UnexpectedValueException(
            sprintf('The demand object must be an instance of Sonority\\Geolocations\\Domain\\Model\\Dto\\LocationDemand, but %s given!',
                $class), 1562346431
            );
        }
        // Set Categories
        $demand->setCategories($settings['categories']);
        // Set order by
        if (!empty($settings['orderBy'])) {
            $demand->setOrderBy($settings['orderBy'] . ' ' . $settings['sortOrder']);
        }
        // Search result limit
        $demand->setLimit($settings['limit']);
        // Set search parameters
        $demand->setSearchFields($settings['searchFields']);
        $demand->setSearchString($settings['searchString']);
        // Set storage page
        $demand->setStoragePage($settings['pidList']);
        return $demand;
    }

    /**
     * Injects the Configuration Manager and is initializing the framework settings
     *
     * @param \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager Instance of the Configuration Manager
     * @return void
     */
    public function injectConfigurationManager(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager)
    {
        parent::injectConfigurationManager($configurationManager);
        // Get override array
        $override = [];
        if (isset($this->settings['override']) && is_array($this->settings['override'])) {
            $override = $this->settings['override'];
            unset($this->settings['override']);
        }
        $this->settings['pid'] = $GLOBALS['TSFE']->id;
        $this->settings['pidList'] = Page::getRecursivePidList($this->settings['startingpoint'], $this->settings['recursive']);
        // Merge settings
        $this->mergeRecursiveWithOverrule($this->settings, $override, true);
    }

    /**
     * Merges two arrays recursively and "binary safe" (integer keys are
     * overridden as well), overruling similar values in the original array
     * with the values of the overrule array.
     *
     * This method takes the original array by reference for speed optimization with large arrays
     *
     * @param array $original Original array. It will be *modified* by this method and contains the result afterwards!
     * @param array $overrule Overrule array, overruling the original array
     * @param bool $overrideWithEmptyValues If set, values from $overrule will overrule if they are empty or zero.
     * @return void
     */
    protected function mergeRecursiveWithOverrule(array &$original, array $overrule, $overrideWithEmptyValues = false)
    {
        foreach ($overrule as $key => $_) {
            if (isset($original[$key]) && is_array($original[$key])) {
                if (is_array($overrule[$key])) {
                    $this->mergeRecursiveWithOverrule($original[$key], $overrule[$key], $overrideWithEmptyValues);
                }
            } elseif (
                isset($original[$key]) && ($overrideWithEmptyValues || ($original[$key] === '' && $overrule[$key] !== ''))
            ) {
                $original[$key] = $overrule[$key];
            }
        }
    }

    /**
     * Emits signal for various actions
     *
     * @param string $signalClassName The last part of the class name
     * @param string $signalName The Name of the signal slot
     * @param array $signalArguments Arguments for the signal slot
     *
     * @return array
     */
    protected function emitActionSignal($signalClassName, $signalName, array $signalArguments)
    {
        $signalArguments['extendedVariables'] = [];
        return $this->signalSlotDispatcher->dispatch('\\Sonority\\Geolocations\\Controller\\' . $signalClassName, $signalName,
                $signalArguments);
    }

    /**
     * Flash a message
     *
     * @param string title
     * @param string message
     *
     * @return void
     */
    private function flashMessage($title, $message, $severity = \TYPO3\CMS\Core\Messaging\FlashMessage::WARNING)
    {
        $this->addFlashMessage(
            $message, $title, $severity, $storeInSession = true
        );
    }

    /**
     * Shows the settings error view
     *
     * @return void
     */
    public function settingsErrorAction()
    {

    }

}
