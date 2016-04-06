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
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * AjaxController
 *
 * @package geolocations
 */
class AjaxController extends BaseController
{

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
     * Array of arguments and their values
     *
     * @var array
     */
    protected $requestArguments;

    /**
     * Initialize action
     *
     * @return void
     */
    public function initializeAction()
    {
        $this->requestArguments = $this->request->getArguments();
        $allowedOrderByFields = GeneralUtility::trimExplode(',', $this->settings['allowedOrderByFields'] . ',');
        $searchObject = json_decode(base64_decode($this->requestArguments['searchObject'], true));
        if (
            !$searchObject ||
            !is_array($searchObject) ||
            count($searchObject) !== 7 ||
            $searchObject[0] === '' ||
            !in_array($searchObject[2], $allowedOrderByFields) ||
            !in_array($searchObject[3], ['asc', 'desc'])
        ) {
            return '';
        }
        $additionalSettings = [
            'pid' => $searchObject[0],
            'pidList' => $searchObject[1],
            'orderBy' => $searchObject[2],
            'sortOrder' => $searchObject[3],
            'limit' => $searchObject[4],
            'cropMaxCharacters' => $searchObject[5],
            'searchString' => $this->requestArguments['keyword'],
            'latitude' => $this->requestArguments['latitude'],
            'longitude' => $this->requestArguments['longitude'],
            'radius' => $this->requestArguments['radius'],
            'searchType' => $this->requestArguments['searchType']
        ];
        if (is_array($this->requestArguments['categories'])) {
            $predefinedCategories = GeneralUtility::intExplode(',', $searchObject[6]);
            $categories = [];
            foreach ($this->requestArguments['categories'] as $category) {
                if (in_array($category, $predefinedCategories)) {
                    $categories[] = $category;
                }
            }
            $additionalSettings['categories'] = implode(',', $categories);
        } else {
            $additionalSettings['categories'] = $searchObject[6];
        }
        $this->settings = array_merge($this->settings, $additionalSettings);
    }

    /**
     * Start radial search
     *
     * @return string
     */
    public function searchAction()
    {
        $locationRecords = null;
        switch ($this->settings['searchType']) {
            case 'radial':
                $radialSearchParameters = [
                    'latitude' => $this->settings['latitude'],
                    'longitude' => $this->settings['longitude'],
                    'radius' => $this->settings['radius'],
                    'categories' => $this->settings['categories'],
                    'pidList' => $this->settings['pidList'],
                    'limit' => $this->settings['limit'],
                    'orderBy' => $this->settings['orderBy'],
                    'sortOrder' => $this->settings['sortOrder'],
                    'calulcateDistance' => $this->settings['search']['calulcateDistance'],
                    'kilometer' => $this->settings['search']['kilometer']
                ];
                $locationRecords = $this->locationRepository->findLocationsInRadius($radialSearchParameters);
                break;
            case 'fulltext':
                $demand = $this->createDemandObjectFromSettings($this->settings);
                // Set current action & class
                $demand->setActionAndClass(__METHOD__, __CLASS__);
                if (!empty($this->requestArguments['keyword'])) {
                    $locationRecords = $this->locationRepository->findDemanded($demand);
                }
                break;
        }
        $locations = null;
        $count = 0;
        if ($locationRecords) {
            $locations = $locationRecords->toArray();
            $count = $locationRecords->count();
        }
        $assignMultiple = [
            'locations' => $locationRecords,
            'count' => $count,
            'radius' => $this->settings['radius'],
            'searchType' => $this->settings['searchType'],
            'searchString' => $this->settings['searchString']
        ];
        return $this->outputJson($assignMultiple);
    }

    /**
     * Render the view and generate json-encoded output
     *
     * @param type $assignMultiple
     * @return type
     */
    private function outputJson($assignMultiple)
    {
        $this->view->assignMultiple($assignMultiple);
        // Render content
        $html = $this->view->render();
        // Remove comments, newlines, tabs and spaces and return the result as json-encoded HTML
        return json_encode(preg_replace('/<!--(.*)-->/Uis', '', preg_replace('/^\s+|\n|\r|\s+$/m', '', $html)));
    }

    /**
     * Renders a single fluid-template
     *
     * @param string $templateName
     * @param array $values
     * @return string
     */
    public function renderFluidTemplate($templateName, $values = [])
    {
        /** @var StandaloneView $view */
        $view = $this->objectManager->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');
        $view->setTemplateRootPaths($this->configuration['view']['templateRootPaths']);
        $view->setLayoutRootPaths($this->configuration['view']['layoutRootPaths']);
        $view->setPartialRootPaths($this->configuration['view']['partialRootPaths']);
        $view->setTemplate($templateName);
        $view->assignMultiple($values);
        return $view->render();
    }

}
