<?php

/**
 * ISMU enrolment plugin version specification.
 *
 * @package    enrol_ismu
 * @copyright  2016 Masaryk University
 * @author     Filip Benco
 */
defined('MOODLE_INTERNAL') || die();

$plugin->version   = 2016092901;        // The current plugin version (Date: YYYYMMDDXX)
$plugin->requires  = 2016051900;        // Requires this Moodle version
$plugin->component = 'enrol_ismu';  // Full name of the plugin (used for diagnostics)
$plugin->cron 	   = 300;