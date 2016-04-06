<?php

namespace Sonority\Geolocations\Form\Element;

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
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Core\Utility\StringUtility;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Generation of TCEform elements of the type "input type=text"
 */
class GeoCodeElement
{

    /**
     * Default width value for a couple of elements like text
     *
     * @var int
     */
    protected $defaultInputWidth = 30;

    /**
     * Minimum width value for a couple of elements like text
     *
     * @var int
     */
    protected $minimumInputWidth = 10;

    /**
     * Maximum width value for a couple of elements like text
     *
     * @var int
     */
    protected $maxInputWidth = 50;

    /**
     * This will render a single-line input form field, possibly with various control/validation features
     *
     * @return array As defined in initializeResultArray() of AbstractNode
     */
    public function render($parameterArray, $data)
    {
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $extConf = $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['geolocations'];
        if ($extConf) {
            $extConf = unserialize($extConf);
        }
        $extKey = 'geolocations';
        $extRelPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($extKey);
        // Add custom javascript and CSS
        $pageRenderer->addJsFile('//maps.google.com/maps/api/js?key=' . $extConf['apiKey'] . '&libraries=places', null, false,
            true, '', true);
        $pageRenderer->addJsFile($extRelPath . 'Resources/Public/JavaScript/geolocations_tca.min.js');
        $pageRenderer->addCssFile($extRelPath . 'Resources/Public/Css/geolocations_tca.css');
        // Add inline-language for javascript
        $pageRenderer->addInlineLanguageLabelFile('EXT:' . $extKey . '/Resources/Private/Language/locallang.xlf');

        $resultArray = [
            'additionalJavaScriptPost' => [],
            'additionalJavaScriptSubmit' => [],
            'additionalHiddenFields' => [],
            'additionalInlineLanguageLabelFiles' => [],
            'stylesheetFiles' => [],
            // Can hold strings or arrays, string = requireJS module, array = requireJS module + callback e.g. array('TYPO3/Foo/Bar', 'function() {}')
            'requireJsModules' => [],
            'extJSCODE' => '',
            'inlineData' => [],
            'html' => '',
        ];

        $config = $parameterArray['fieldConf']['config'];
        $size = MathUtility::forceIntegerInRange($config['size'] ? : $this->defaultInputWidth, $this->minimumInputWidth,
                $this->maxInputWidth);
        $evalList = GeneralUtility::trimExplode(',', $config['eval'], true);
        $classes = array();
        $attributes = array();

        foreach ($evalList as $func) {
            switch ($func) {
                case 'required':
                    $validationRules[] = array('type' => 'required');
                    break;
            }
        }
        $paramsList = array(
            'field' => $parameterArray['itemFormElName'],
            'evalList' => '',
            'is_in' => '',
        );
        // Set classes
        $classes[] = 'form-control';
        $classes[] = 't3js-clearable';
        $classes[] = 'hasDefaultValue';

        // Calculate attributes
        $attributes['data-formengine-validation-rules'] = json_encode($validationRules);
        $attributes['data-formengine-input-params'] = json_encode($paramsList);
        $attributes['data-formengine-input-name'] = htmlspecialchars($parameterArray['itemFormElName']);
        $attributes['id'] = StringUtility::getUniqueId('formengine-input-');
        $attributes['value'] = '';
        if (isset($config['max']) && (int) $config['max'] > 0) {
            $attributes['maxlength'] = (int) $config['max'];
        }
        if (!empty($classes)) {
            $attributes['class'] = implode(' ', $classes);
        }

        // This is the EDITABLE form field.
        if (!empty($config['placeholder'])) {
            $attributes['placeholder'] = trim($config['placeholder']);
        }

        // Build the attribute string
        $attributeString = '';
        foreach ($attributes as $attributeName => $attributeValue) {
            $attributeString .= ' ' . $attributeName . '="' . htmlspecialchars($attributeValue) . '"';
        }

        $html = '
			<input type="text"'
            . $attributeString
            . $parameterArray['onFocus'] . ' />';

        // This is the ACTUAL form field - values from the EDITABLE field must be transferred to this field which is the one that is written to the database.
        $html .= '<input type="hidden" name="' . $parameterArray['itemFormElName'] . '" value="' . htmlspecialchars($parameterArray['itemFormElValue']) . '" />';

        // Add HTML wrapper
        $html = '
            <div class="input-group">
                ' . $html . '
                <span class="input-group-btn">
                    <label class="btn btn-default" for="' . $attributes['id'] . '" onclick="Geocoder.geocodeAddress()" title="' . LocalizationUtility::translate('geocoder.geocode.titleText',
                $extKey) . '">' . LocalizationUtility::translate('geocoder.geocode.label', $extKey) . '</label>
                </span>
                <span class="input-group-btn">
                    <label class="btn btn-default" onclick="Geocoder.showMap()" title="' . LocalizationUtility::translate('geocoder.map.titleText',
                $extKey) . '">
                        <img width="16" height="16" src="/typo3conf/ext/' . $extKey . '/Resources/Public/Images/google-maps.png">
                    </label>
                </span>
            </div>
            <script type="text/javascript">
            /*<![CDATA[*/
            TYPO3.jQuery(document).ready(function($) { Geocoder.initialize("' . $parameterArray['row']['uid'] . '"); });
            /*]]>*/
            </script>';

        // Add a wrapper to remain maximum width
        $width = (int) $this->formMaxWidth($size);
        $html = '<div class="form-control-wrap"' . ($width ? ' style="max-width: ' . $width . 'px"' : '') . '>' . $html . '</div>';
        // Add message-container
        $html .= '<div id="geocode-status-' . $parameterArray['row']['uid'] . '"></div>';
        // Add map-canvas
        $html .= '<div id="map_canvas" style="height:60%;top:30px"></div>';

        $resultArray['html'] = $html;
        return $html;
    }

    /**
     * Returns the max width in pixels for an elements like input and text
     *
     * @param int $size The abstract size value (1-48)
     * @return int Maximum width in pixels
     */
    protected function formMaxWidth($size = 48)
    {
        $compensationForLargeDocuments = 1.33;
        $compensationForFormFields = 12;

        $size = round($size * $compensationForLargeDocuments);
        return ceil($size * $compensationForFormFields);
    }

}
