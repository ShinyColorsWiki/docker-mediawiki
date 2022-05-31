<?php
# This file was automatically generated by the MediaWiki 1.31.0
# installer. If you make manual changes, please keep track in case you
# need to recreate them later.
#
# See includes/DefaultSettings.php for all configurable settings
# and their default values, but don't forget to make changes in _this_
# file, not there.
#
# Further documentation for configuration settings may be found at:
# https://www.mediawiki.org/wiki/Manual:Configuration_settings

# Protect against web entry
if ( !defined( 'MEDIAWIKI' ) ) {
exit;
}

$wgSitename = "Shinycolors Wiki";
$wgMetaNamespace = "ShinyWiki";
$wgNamespacesWithSubpages[NS_MAIN] = true;

## The URL base path to the directory containing the wiki;
## defaults for all runtime URL paths are based off of this.
## For more information on customizing the URLs
## (like /w/index.php/Page_title to /wiki/Page_title) please see:
## https://www.mediawiki.org/wiki/Manual:Short_URL
$wgScriptPath = "/w";
$wgScriptExtension = ".php";
$wgArticlePath = "/wiki/$1";
$wgUsePathInfo = true;

## The protocol and server name to use in fully-qualified URLs
$wgServer = "https://shinycolors.wiki";

## The URL path to static resources (images, scripts, etc.)
$wgResourceBasePath = $wgScriptPath;

## The URL path to the logo.  Make sure you change this from the default,
## or else you'll overwrite your logo when you upgrade!
#$wgLogo = $wgScriptPath . "/images/3/35/WikiLogo.png";
#$wgLogo = "https://image.shinycolors.wiki/1/1a/WikiLogo150.png";
#$wgLogo = "https://image.shinycolors.wiki/9/9a/WikiLogo2021.svg";
$wgLogos = [
    '1x' => "https://image.shinycolors.wiki/1/1a/WikiLogo150.png",
    'svg' => "https://image.shinycolors.wiki/9/9a/WikiLogo2021.svg",
    'wordmark' => [
        'src' => "https://image.shinycolors.wiki/4/47/WikiWideLogo2021.svg",
        #'1x' => "https://image.shinycolors.wiki/4/47/WikiWideLogo2021.svg",
        'width' => 320,
        'height' => 54,
    ],
#    'tagline' => [
#        'src' => "https://image.shinycolors.wiki/4/47/WikiWideLogo2021.svg",
#       #'1x' => "https://image.shinycolors.wiki/4/47/WikiWideLogo2021.svg",
#       'width' => 200,
#       'height' => 34,
#    ],
];

## UPO means: this is also a user preference option

$wgEnableEmail = true;
$wgEnableUserEmail = true; # UPO

$wgEmergencyContact = "no-reply@shinycolors.wiki";
$wgPasswordSender = "no-reply@shinycolors.wiki";

$wgEnotifUserTalk = false; # UPO
$wgEnotifWatchlist = false; # UPO
$wgEmailAuthentication = true;

# Load Secret settings config.
require_once '/setting/secret.php';

# MySQL specific settings
$wgDBprefix = "";

# MySQL table options to use during installation or update
$wgDBTableOptions = "ENGINE=InnoDB, DEFAULT CHARSET=binary";

## Shared memory settings
$wgObjectCaches['redis'] = array(
        'class'      => 'RedisBagOStuff',
        'servers'    => array( 'redis:6379' ),
        'loggroup'   => 'redis',
        'persistent' => true,
);
$wgMainCacheType = CACHE_ACCEL;
$wgMemCachedServers = [];
#$wgSessionCacheType = CACHE_DB; # For save sessions even restart.
$wgSessionCacheType = 'redis';

$wgMessageCacheType = CACHE_NONE;
$wgUseLocalMessageCache = true;
$wgParserCacheType = CACHE_DB; #'redis';
$wgLanguageConverterCacheType = CACHE_DB;

$wgJobTypeConf['default'] = [
        'class'          => 'JobQueueRedis',
#       'order'          => 'fifo',
        'redisServer'    => 'redis:6379',
        'compression'    => 'gzip',
#       'checkDelay'     => true,
        'claimTTL'       => 3600,
        'daemonized'     => true
];
$wgJobQueueAggregator = [
        'class'       => 'JobQueueAggregatorRedis',
        'redisServer' => 'redis:6379',
        'redisConfig' => [
                'compression' => 'gzip',
        ],
];


## To enable image uploads, make sure the 'images' directory
## is writable, then set this to true:
$wgEnableUploads = true;
$wgUseImageMagick = true;
$wgImageMagickConvertCommand = "/usr/bin/convert";
$wgGenerateThumbnailOnParse = true;

# InstantCommons allows wiki to use images from https://commons.wikimedia.org
$wgUseInstantCommons = true;

# Periodically send a pingback to https://www.mediawiki.org/ with basic data
# about this MediaWiki instance. The Wikimedia Foundation shares this data
# with MediaWiki developers to help guide future development efforts.
$wgPingback = false;

## If you use ImageMagick (or any other shell command) on a
## Linux server, this will need to be set to the name of an
## available UTF-8 locale
$wgShellLocale = "C.UTF-8";
$wgMaxShellMemory = 1228800;

## Set $wgCacheDirectory to a writable directory on the web server
## to make your wiki go slightly faster. The directory should not
## be publically accessible from the web.
$wgCacheDirectory = "$IP/cache";

# Site language code, should be one of the list in ./languages/data/Names.php
$wgLanguageCode = "en";

# Changing this will log out all existing sessions.
$wgAuthenticationTokenVersion = "1";

## For attaching licensing metadata to pages, and displaying an
## appropriate copyright notice / icon. GNU Free Documentation
## License and Creative Commons licenses are supported so far.
#$wgRightsPage = "ShinyWiki:Copyright"; # Set to the title of a wiki page that describes your license/copyright
$wgRightsUrl = "http://creativecommons.org/licenses/by-nc-sa/4.0/";
$wgRightsText = "a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License";
$wgRightsIcon = "https://licensebuttons.net/l/by-nc-sa/4.0/88x31.png";

# Path to the GNU diff3 utility. Used for conflict resolution.
$wgDiff3 = "/usr/bin/diff3";

## Default skin: you can change the default skin. Use the internal symbolic
## names, ie 'vector', 'monobook':
$wgDefaultSkin = "vector";

# Cookie
$wgCookieDomain = ".shinycolors.wiki"; # this should have to.


# Varnish Cache / CloudFlare CDN sets
$wgUseCdn = true;
#$wgCdnServers = array();
#$wgCdnServers[] = "127.0.0.1:6081";
$wgCdnServersNoPurge = array( # list of CloudFlare ipv4 list.
        "173.245.48.0/20",
        "103.21.244.0/22",
        "103.22.200.0/22",
        "103.31.4.0/22",
        "141.101.64.0/18",
        "108.162.192.0/18",
        "190.93.240.0/20",
        "188.114.96.0/20",
        "197.234.240.0/22",
        "198.41.128.0/17",
        "162.158.0.0/15",
        "172.64.0.0/13",
        "131.0.72.0/22",
        "104.16.0.0/13",
        "104.24.0.0/14",
        "2400:cb00::/32",
        "2606:4700::/32",
        "2803:f800::/32",
        "2405:b500::/32",
        "2405:8100::/32",
        "2a06:98c0::/29",
        "2c0f:f248::/32"
);

# Favicon & Touch Icon
$wgFavicon = "/favicon.ico";
$wgAppleTouchIcon =  "https://image.shinycolors.wiki/d/dc/WikiFavLogo.png"; #$wgScriptPath . "/images/d/dc/WikiFavLogo.png";

# FileUpload
$wgGroupPermissions['user']['upload'] = false;
$wgGroupPermissions['user']['reupload'] = false;
$wgGroupPermissions['user']['reupload-shared'] = false;

$wgGroupPermissions['autoconfirmed']['upload'] = true;
$wgGroupPermissions['autoconfirmed']['reupload'] = true;
#$wgGroupPermissions['autoconfirmed']['reupload-shared'] = true;

$wgGroupPermissions['bot']['upload'] = true;
$wgGroupPermissions['bot']['reupload'] = true;
$wgGroupPermissions['bot']['reupload-shared'] = true;

#Temporal solution
$wgGroupPermissions['emailconfirmed']['upload'] = true;
$wgGroupPermissions['emailconfirmed']['reupload'] = true;

# Auto confirm and promotion
$wgAutoConfirmAge = 86400*3;
$wgAutoConfirmCount = 20;

$wgAutopromote = array(
        "autoconfirmed" => array( "&",
                array( APCOND_EDITCOUNT, &$wgAutoConfirmCount ),
                array( APCOND_AGE, &$wgAutoConfirmAge ),
                APCOND_EMAILCONFIRMED
        ),
        "emailconfirmed" => APCOND_EMAILCONFIRMED,
);
$wgImplicitGroups[] = 'emailconfirmed';

# Load Extension settings config.
require_once '/setting/wiki/ExtensionSettings.php';

# Load Development settings
require_once '/setting/wiki/Development.php';

#$wgReadOnly = 'We\'re updating wiki and system software for better user experiences. <b>ETA is 0:00 UTC</b>';