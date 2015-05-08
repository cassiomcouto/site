<?php
/**
 * Configuration File
 *
 * This is a PHP file that sets up variables specific for a template.
 * It can also be used to run PHP code for a template.
 *
 * @version 1.0
 *
 */

// The name of the template, as it will appear in the control panel
$GLOBALS['TPL_CFG']['Name']	= 'jrred';

// The version of the template, as it will appear in the control panel
$GLOBALS['TPL_CFG']['Version'] = '3.0';

// The recommended width of a logo when it's displayed in the header of this template
$GLOBALS['TPL_CFG']['LogoWidth'] = 360;

// The recommended height of a logo when it's displayed in the header of this template
$GLOBALS['TPL_CFG']['LogoHeight'] = 130;

// The maximum width an image uploaded in DevEdit can be before it's resized.
// This is used to make sure product images uploaded in DevEdit don't stretch
// out past the main content area of the template
$GLOBALS['TPL_CFG']['MaxImageWidth'] = 575;

// The maximum number of products that can be compared side-by-side without
// messing up the layout of the template
$GLOBALS['TPL_CFG']['MaxComparisonProducts'] = 5;

// The "Powered by" line that this template should use.
// These lines can be adjusted in /includes/whitelabel.php
$GLOBALS['TPL_CFG']['PoweredBy'] = 4;

// This defines the element that uses the header image as a background image
// All styles (position, repeat) for the background image should be defined in the
// template CSS except the location (URL), which is defined automatically and applied
// to the selector (ID, class) defined here:
$GLOBALS['TPL_CFG']['HeaderImageElement'] = '#Header';