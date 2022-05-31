<?php

# SERVER can be overriden by environ value
if ( getenv( 'MEDIAWIKI_SERVER' ) ) {
	$wgServer = getenv( 'MEDIAWIKI_SERVER' );
	$wgForceHTTPS = substr( $wgServer, 0, 5 ) === 'https';
}

if ( getenv( 'MEDIAWIKI_DEBUG' ) ) {
	# https://www.mediawiki.org/wiki/Manual:DevelopmentSettings.php
	require_once "includes/DevelopmentSettings.php";

	# Overwrite existing settings
	$wgDebugToolbar = true;
	$wgShowDBErrorBacktrace = true;
	$wgUseCdn = false;
	$wgEmailConfirmToEdit = false;
	$wgCookieDomain = '';

	# Disable AWS
	$wgAWSBucketName = null;
	$wgAWSBucketPrefix = null;
	$wgAwsSesCredential = null;
	$wgAwsSesRegion = null;

	# Disable captcha
	$wgCaptchaTriggers['edit'] = false;
	$wgCaptchaTriggers['create'] = false;
	$wgCaptchaTriggers['createtalk'] = false;
	$wgCaptchaTriggers['addurl'] = false;
	$wgCaptchaTriggers['createaccount'] = false;
	$wgCaptchaTriggers['badlogin'] = false;

	# Disable GTag
	$wgGTagAnalyticsId = null;
	$wgGroupPermissions['*']['gtag-exempt'] = true;

	# Disable CrowdSec
	$wgCrowdSecEnable = false;

	$wgShowExceptionDetails = true;
	#$wgDebugLogFile = "/opt/wiki/log/debug-{$wgDBname}.log";
}