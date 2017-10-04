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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use Sonority\Geolocations\Utility\Cache;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Core\Page\PageRenderer;

/**
 * LocationController
 *
 * @package geolocations
 */
class LocationController extends BaseController
{

    const SIGNAL_LOCATION_MAP_ACTION = 'mapAction';
    const SIGNAL_LOCATION_LIST_ACTION = 'listAction';
    const SIGNAL_LOCATION_SEARCH_ACTION = 'searchAction';

    /**
     * Location repository
     *
     * @var \Sonority\Geolocations\Domain\Repository\LocationRepository
     * @inject
     */
    protected $locationRepository;

    /**
     * Category Repository
     *
     * @var \Sonority\Geolocations\Domain\Repository\CategoryRepository
     * @inject
     */
    protected $categoryRepository;

    /**
     * Initialize map action
     *
     * @return void
     */
    public function initializeMapAction()
    {
        // Remove newlines followed by tabs/whitespaces and wrap the styledefinition in curly brackets
        $userStyles = '{' . preg_replace('/\n+[\t\s]*/', '', $this->settings['map']['userStyles']) . '}';
        $this->settings['map']['userStyles'] = json_encode($userStyles);
    }

    /**
     * Initialize search action
     *
     * @return void
     */
    public function initializeSearchAction()
    {
        // Prepare options for radius-selector
        $radiusOptions = [];
        if (!empty($this->settings['search']['radialSearchDistances'])) {
            $distances = GeneralUtility::trimExplode(',', $this->settings['search']['radialSearchDistances']);
            foreach ($distances as $distance) {
                // Get default selected option
                if (strpos($distance, '*') === 0) {
                    $distance = str_replace('*', '', $distance);
                    $this->settings['search']['radialDefault'] = $distance;
                }
                // Add item as translated option
                $radiusOptions[intval($distance)] = sprintf(LocalizationUtility::translate('geocoder.search.distances',
                        'geolocations'), intval($distance));
            }
        }
        $this->settings['search']['radialSearchDistances'] = $radiusOptions;
        if ($this->settings['search']['showRadialSearch'] && $this->settings['search']['showFulltextSearch']) {
            $this->settings['search']['searchTypeSelector'] = true;
        }
    }

    /**
     * Output a map container
     *
     * @return void
     */
    public function mapAction()
    {
        $this->initializeDefaultAction();
        // The map itself does not list any locations. You need to place a list-plugin on the same page
    }

    /**
     * Output a list view of locations
     *
     * @return void
     */
    public function listAction()
    {
        $this->initializeDefaultAction();
        $demand = $this->createDemandObjectFromSettings($this->settings);
        // Set current action & class
        $demand->setActionAndClass(__METHOD__, __CLASS__);

        if ($this->settings['list']['initialState'] !== '') {
            $locationRecords = $this->locationRepository->findDemanded($demand);
        } else {
            $locationRecords = null;
        }
        $viewVariables = [
            'locations' => $locationRecords,
            'demand' => $demand
        ];
        $assignedValues = $this->emitActionSignal('LocationController', self::SIGNAL_LOCATION_LIST_ACTION, $viewVariables);
        $this->view->assignMultiple($assignedValues);
        Cache::addPageCacheTagsByDemandObject($demand);
    }

    /**
     * Output a list view of locations
     *
     * @return void
     */
    public function searchAction()
    {
        $this->initializeDefaultAction();
        // Encode search parameters for ajax requests
        $searchObject = base64_encode(json_encode([
            $this->settings['pid'],
            $this->settings['pidList'],
            $this->settings['orderBy'],
            $this->settings['sortOrder'],
            $this->settings['limit'],
            $this->settings['cropMaxCharacters'],
            $this->settings['categories']
        ]));
        $categoryIds = explode(',', $this->settings['categories']);
        $categories = $this->categoryRepository->findByIdList($categoryIds);
        if (count($categories) <= 1) {
            $this->settings['search']['showCategories'] = false;
        }
        $viewVariables = [
            'categories' => $categories,
            'searchObject' => $searchObject
        ];
        $assignedValues = $this->emitActionSignal('LocationController', self::SIGNAL_LOCATION_SEARCH_ACTION, $viewVariables);
        $this->view->assignMultiple($assignedValues);
    }

    /**
     * Initializes the current action
     *
     * @return void
     */
    public function initializeDefaultAction()
    {
        $this->extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['geolocations']);
        if (!is_array($this->extConf) || $this->settings === null) {
            $this->redirect('settingsError');
        }
        if (empty($this->settings['search']['autocompleter']['language'])) {
            $this->settings['search']['autocompleter']['language'] = $GLOBALS['TSFE']->sys_language_isocode;
        }
        $this->settings['apiKey'] = $this->extConf['apiKey'];
        // Include google maps API
        $this->includeGoogleApi();
        // Only do this in Frontend Context
        if (!empty($GLOBALS['TSFE']) && is_object($GLOBALS['TSFE'])) {
            // We only want to set the tag once in one request, so we have to cache that statically if it has been done
            static $cacheTagsSet = false;
            /** @var $typoScriptFrontendController \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController */
            $typoScriptFrontendController = $GLOBALS['TSFE'];
            if (!$cacheTagsSet) {
                $typoScriptFrontendController->addCacheTags(['tx_geolocations']);
                $cacheTagsSet = true;
            }
        }
    }

    /**
     * Include google maps API
     *
     * @return void
     */
    private function includeGoogleApi()
    {
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $googleApi = '//maps.google.com/maps/api/js';
        $apiParams = [];
        if (!empty($this->extConf['apiKey'])) {
            $apiParams[] = 'key=' . $this->extConf['apiKey'];
        }
        $apiParams[] = 'libraries=places';
        $apiParams[] = 'language=' . $GLOBALS['TSFE']->tmpl->setup['config.']['language'];
        $pageRenderer->addJsFooterLibrary('google_maps_api', $googleApi . '?' . implode('&', $apiParams), null, false, true, '',
            true, '|', false);
    }

}
