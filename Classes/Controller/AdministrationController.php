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

use Sonority\Geolocations\Domain\Model\Dto\LocationDemand;
use Sonority\Geolocations\Service\GeocodeService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use UnexpectedValueException;

/**
 * AdministrationController
 *
 * @package geolocations
 */
class AdministrationController extends ActionController
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
     * @var ConfigurationManagerInterface
     */
    protected $configurationManager;

    /**
     * The current page uid
     *
     * @var int
     */
    protected $pid = 0;

    /**
     * Persistence Manager
     *
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
     * @inject
     */
    protected $persistenceManager;

    /**
     * Create the demand object which defines which records will get shown
     *
     * @param array $settings
     * @param string $actionName The name of the current action
     * @param string $class optional class which must be an instance of \Sonority\Geolocations\Domain\Model\Dto\LocationDemand
     * @return LocationDemand
     */
    protected function createDemandObjectFromSettings($settings, $class = 'Sonority\\Geolocations\\Domain\\Model\\Dto\\LocationDemand')
    {
        $class = isset($settings['demandClass']) && !empty($settings['demandClass']) ? $settings['demandClass'] : $class;
        /* @var $demand LocationDemand */
        $demand = $this->objectManager->get($class, $settings);
        if (!$demand instanceof LocationDemand) {
            throw new UnexpectedValueException(
            sprintf('The demand object must be an instance of Sonority\\Geolocations\\Domain\\Model\\Dto\\LocationDemand, but %s given!',
                $class), 1562346431
            );
        }
        // Set order by
        if (!empty($settings['orderBy'])) {
            $demand->setOrderBy($settings['orderBy'] . ' ' . $settings['sortOrder']);
        }
        // Set storage page
        $demand->setStoragePage($settings['pidList']);
        return $demand;
    }

    /**
     * Initialize action
     *
     * @return void
     */
    public function initializeAction()
    {
        if ($this->settings === null) {
            $this->redirect('settingsError');
        }
        $this->settings['pidList'] = (int) GeneralUtility::_GET('id');
    }

    /**
     * Overview action for backend module
     *
     * @return void
     */
    public function overviewAction()
    {
        $demand = $this->createDemandObjectFromSettings($this->settings);
        // Set current action & class
        $demand->setActionAndClass(__METHOD__, __CLASS__);
        $locationRecords = $this->locationRepository->findDemanded($demand, false);
        $this->view->assign('locations', $locationRecords);
    }

    /**
     * Geocode a single address
     *
     * @return void
     */
    public function fillMissingCoordinatesAction()
    {
        $locationRecords = $this->locationRepository->findAllMissingCoordinates();
        $count = count($locationRecords);
        $this->view->assign('count', $count);
    }

    /**
     * Overview action for backend module
     *
     * @return void
     */
    public function geocodeAddressAction()
    {

    }

    /*
     *
     * AJAX-Functions
     *
     */

    /**
     * Geocode a single address at AJAX-request
     *
     * @return void
     */
    public function ajaxGeocodeAddressAction()
    {
        $address = GeneralUtility::_GP('address');
        if (!empty($address)) {
            $geocodeService = GeneralUtility::makeInstance(GeocodeService::class);
            $coordinates = $geocodeService->getCoordinatesForAddress(trim($address), false);
            if (is_array($coordinates)) {
                return json_encode($coordinates);
            }
        }
        return json_encode('');
    }

    /**
     * Starts the geocoding-process in the background and fills all the missing coordinates
     *
     * @return bool TRUE if process is finished
     */
    public function ajaxStartGeocodeProcessAction()
    {
        $locationRecords = $this->locationRepository->findAllMissingCoordinates();
        $needPersistence = false;
        $count = count($locationRecords);
        $geocodeService = GeneralUtility::makeInstance(GeocodeService::class);

        $i = 1;
        foreach ($locationRecords as $location) {
            $address = $location->getAddress();
            $zip = $location->getZip();
            $city = $location->getCity();
            if (!empty($zip) || empty(!$city)) {
                $address .= ',';
                if (!empty($zip)) {
                    $address .= ' ' . $zip;
                }
                if (!empty($city)) {
                    $address .= ' ' . $city;
                }
            }
            $coordinates = $geocodeService->getCoordinatesForAddress(trim($address));
            if (is_array($coordinates) && !empty($coordinates['latitude']) && !empty($coordinates['longitude'])) {
                $needPersistence = true;
                $location->setLatitude($coordinates['latitude']);
                $location->setLongitude($coordinates['longitude']);
                if (!empty($coordinates['place_id'])) {
                    $location->setPlaceId($coordinates['place_id']);
                }
                $this->locationRepository->update($location);
            }

            // Increase the counter stored in session variable
            $percentage = round((100 / $count) * $i);
            session_start();
            $_SESSION['processDone'] = $percentage;
            session_write_close();
            sleep(0.2);
            $i++;
        }
        // Persist the modified object
        if ($needPersistence) {
            $this->persistenceManager->persistAll();
        }
        // Reset the counter in session variable to -1 if process is done
        session_start();
        $_SESSION['processDone'] = -1;
        session_write_close();
        return json_encode(true);
    }

    /**
     * Checks the status of the geocoding-process
     *
     * @return int Status of the process in percent
     */
    public function ajaxCheckGeocodeProcessAction()
    {
        session_start();
        if (!isset($_SESSION['processDone'])) {
            $_SESSION['processDone'] = -1;
        }
        session_write_close();
        sleep(0.5);
        return json_encode($_SESSION['processDone']);
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
