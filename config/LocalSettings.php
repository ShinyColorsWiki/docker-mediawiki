<?php

if ( !defined( 'MEDIAWIKI' ) ) {
exit;
}

$wgNamespacesWithSubpages[NS_MAIN] = true;

## Uncomment this to disable output compression
# $wgDisableOutputCompression = true;

$wgSitename = "Shinycolors Wiki";
$wgMetaNamespace = "ShinyWiki";

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
$wgLogo = "https://images.shinycolors.wiki/1/1a/WikiLogo150.png";

## UPO means: this is also a user preference option

$wgEnableEmail = true;
$wgEnableUserEmail = true; # UPO

$wgEmergencyContact = "no-reply@shinycolors.wiki";
$wgPasswordSender = "no-reply@shinycolors.wiki";

$wgEnotifUserTalk = false; # UPO
$wgEnotifWatchlist = false; # UPO
$wgEmailAuthentication = true;


## Shared memory settings
$wgObjectCaches['redis'] = array(
        'class'      => 'RedisBagOStuff',
        'servers'    => array( '/run/redis/redis.sock' ),
        'loggroup'   => 'redis',
        'persistent' => true,
);
$wgMainCacheType = 'redis';
$wgMemCachedServers = [];
#$wgSessionCacheType = CACHE_DB; # For save sessions even restart.
$wgSessionCacheType = 'redis';

$wgMessageCacheType = CACHE_NONE;
$wgUseLocalMessageCache = true;
$wgParserCacheType = CACHE_DB;
$wgLanguageConverterCacheType = CACHE_DB;

$wgJobTypeConf['default'] = [
        'class'          => 'JobQueueRedis',
#       'order'          => 'fifo',
        'redisServer'    => '/run/redis/redis.sock',
        'redisConfig'    => [
                'compression' => 'gzip',
        ],
#       'checkDelay'     => true,
        'claimTTL'       => 3600,
        'daemonized'     => true
];
$wgJobQueueAggregator = [
        'class'       => 'JobQueueAggregatorRedis',
        'redisServer' => '/run/redis/redis.sock',
        'redisConfig' => [
                'compression' => 'gzip',
        ],
];


## To enable image uploads, make sure the 'images' directory
## is writable, then set this to true:
$wgEnableUploads = true;
$wgUseImageMagick = true;
$wgImageMagickConvertCommand = "/usr/bin/convert";

# InstantCommons allows wiki to use images from https://commons.wikimedia.org
$wgUseInstantCommons = true;

# Periodically send a pingback to https://www.mediawiki.org/ with basic data
# about this MediaWiki instance. The Wikimedia Foundation shares this data
# with MediaWiki developers to help guide future development efforts.
$wgPingback = false;

## If you use ImageMagick (or any other shell command) on a
## Linux server, this will need to be set to the name of an
## available UTF-8 locale
$wgShellLocale = "en_US.UTF-8";
$wgMaxShellMemory = 1228800;

## Set $wgCacheDirectory to a writable directory on the web server
## to make your wiki go slightly faster. The directory should not
## be publically accessible from the web.
$wgCacheDirectory = "$IP/cache";

# Site language code, should be one of the list in ./languages/data/Names.php
$wgLanguageCode = "en";

# Changing this will log out all existing sessions.
$wgAuthenticationTokenVersion = "1";

# Site upgrade key. Must be set to a string (default provided) to turn on the
# web installer while LocalSettings.php is in place
$wgUpgradeKey = "19fa054a344e6277";

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

# Enabled skins.
# The following skins were automatically enabled:
wfLoadSkin( 'MonoBook' );
wfLoadSkin( 'Timeless' );
wfLoadSkin( 'Vector' );


# Enabled extensions. Most of the extensions are enabled by adding
# wfLoadExtensions('ExtensionName');
# to LocalSettings.php. Check specific extension documentation for more details.
# The following extensions were automatically enabled:
wfLoadExtension( 'SpamBlacklist' );
wfLoadExtension( 'TitleBlacklist' );


# End of automatically generated settings.
# Add more configuration options below.

# Fuck you REST.php
#$wgEnableRestAPI = false;

# CloudFlare CDN sets
$wgUseCdn = true;
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
        "104.16.0.0/12",
        "172.64.0.0/13",
        "131.0.72.0/22"
);

# Sentry (Load Eailier for check error)
wfLoadExtension( 'Sentry' );

# Favicon & Touch Icon
$wgFavicon = "/favicon.ico";
$wgAppleTouchIcon = $wgScriptPath . "/images/d/dc/WikiFavLogo.png";

# Mailer (AWS SES)
wfLoadExtension( 'AwsSesMailer' );

# DismissableSiteNotice
wfLoadExtension( 'DismissableSiteNotice' ); # for SiteNotice
$wgMajorSiteNoticeID = 0; # Hey, Auto update enabled.
$wgDismissableSiteNoticeForAnons = true;

# Interwiki.
wfLoadExtension( 'Interwiki' ); # for Interwiki
$wgGroupPermissions['sysop']['interwiki'] = true;

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

# Captcha
wfLoadExtensions([ 'ConfirmEdit', 'ConfirmEdit/ReCaptchaNoCaptcha' ]);
$wgCaptchaClass = 'ReCaptchaNoCaptcha';


# Fuck spam
#$wgGroupPermissions['emailconfirmed']['skipcaptcha'] = true;
#$ceAllowConfirmedEmail = true;
$wgGroupPermissions['autoconfirmed']['skipcaptcha'] = true;

$wgCaptchaTriggers['edit'] = true;
$wgCaptchaTriggers['create'] = true;

# AbuseFilter
wfLoadExtension( 'AbuseFilter' );
$wgAbuseFilterLogIPMaxAge = 6 * 30 * 24 * 3600;

# PaserFunctions
wfLoadExtension( 'ParserFunctions' );
$wgPFEnableStringFunctions = true;

# WikiEditor / CodeEditor
wfLoadExtension( 'WikiEditor' );
#wfLoadExtension( 'CodeEditor' );

# CheckUser
wfLoadExtension( 'CheckUser' );
$wgGroupPermissions['sysop']['checkuser'] = true;
$wgGroupPermissions['sysop']['checkuser-log'] = true;

# Google Analytics
require_once "$IP/extensions/googleAnalytics/googleAnalytics.php";


$wgGoogleAnalyticsAnonymizeIP = false;
$wgGoogleAnalyticsIgnoreSpecials = array( 'Userlogin', 'Userlogout', 'Preferences', 'ChangePassword', 'OATH');
$wgGroupPermissions['sysop']['noanalytics'] = true;
$wgGroupPermissions['bot']['noanalytics'] = true;
#$wgGroupPermissions['autoconfirmed']['noanalytics'] = true;

# Google Analytics 4 (GTag)
wfLoadExtension( 'GTag' );

$wgGroupPermissions['sysop']['gtag-exempt'] = true;
$wgGroupPermissions['bot']['gtag-exempt'] = true;

# Cite
wfLoadExtension( 'Cite' );

# SyntaxHighlight
wfLoadExtension( 'SyntaxHighlight_GeSHi' );

# ImageMap, for who shaping very... well.
wfLoadExtension( 'ImageMap' );

# Beta Features
wfLoadExtension( 'BetaFeatures' );

# Performance Inspector
#wfLoadExtension( 'PerformanceInspector' );

# User Email Setting
$wgGroupPermissions['user']['sendemail'] = false;
$wgGroupPermissions['emailconfirmed']['sendemail'] = true;

# Variables and Loops
wfLoadExtension( 'Variables' );
#require_once "$IP/extensions/Loops/Loops.php";
wfLoadExtension( 'Loops' );

# EmbedVideo
wfLoadExtension( 'EmbedVideo' );
#$wgFFmpegLocation = '/usr/local/bin/ffmpeg';
#$wgFFprobeLocation = '/usr/local/bin/ffprobe';

# ReplaceText
wfLoadExtension( 'ReplaceText' );

# CodeMirror
wfLoadExtension( 'CodeMirror' );
$wgDefaultUserOptions['usecodemirror'] = 1;

# Description 2 for Meta tags
wfLoadExtension( 'Description2' );

# OpenGraphMeta
wfLoadExtension( 'OpenGraphMeta' );

# PageImages for OpenGraph
wfLoadExtension( 'PageImages' );

# StopForumSpam to prevent spam
wfLoadExtension( 'StopForumSpam' );
$wgGroupPermissions['bot']['sfsblock-bypass'] = true;

# Spam
$wgSpamBlacklistFiles = array(
        "https://meta.wikimedia.org/wiki/Spam_blacklist",
        "https://meta.wikimedia.org/wiki/MediaWiki:Spam-blacklist",
        "https://en.wikipedia.org/wiki/MediaWiki:Spam-blacklist"
);

# LookUpUser for Spammers
wfLoadExtension( 'LookupUser' );
$wgGroupPermissions['*']['lookupuser'] = false;
$wgGroupPermissions['lookupuser']['lookupuser'] = true;

# Nuke for remove Spam articles
wfLoadExtension( 'Nuke' );

# Cookie Warning
wfLoadExtension( 'CookieWarning' );
$wgCookieWarningEnabled = true;

# AntiSpoof
wfLoadExtension( 'AntiSpoof' );

# CategoryTree
wfLoadExtension( 'CategoryTree' );

# Gadgets
wfLoadExtension( 'Gadgets' );
#$wgGroupPermissions['sysop']['editinterface'] = true;

# Revison Slider
wfLoadExtension( 'RevisionSlider' );

# TwoColConflict
wfLoadExtension( 'TwoColConflict' );

# Popups
wfLoadExtension( 'TextExtracts' ); # Dependency
wfLoadExtension( 'Popups' );
$wgPopupsBetaFeature = true;
$wgPopupsExperiment = true;
#$wgPopupsHideOptInOnPreferencesPage = true;
$wgPopupsOptInDefaultState = '0';

# Echo for Notifications.
wfLoadExtension( 'Echo' );
$wgEchoEnableEmailBatch = false;
$wgDefaultUserOptions['echo-email-frequency'] = -1;

# Thanks
wfLoadExtension( 'Thanks' );

# Discord (Alt)
wfLoadExtension( 'Discord' );

# Contact Page
wfLoadExtension( 'ContactPage' );
$wgContactConfig['default'] = array(
        'RecipientUser' => 'ContactUser',
        'SenderName' => 'Contact Form on ' . $wgSitename,
#       'SenderEmail' => null,
        'SenderEmail' => null,
        'RequireDetails' => true,
        'IncludeIP' => true,
        'MustBeLoggedIn' => true,
        'AdditionalFields' => array(
                'Text' => array(
                        'label-message' => 'emailmessage',
                        'type' => 'textarea',
                        'rows' => 20,
                        'required' => true,
                ),
        ),
        'DisplayFormat' => 'table',
        'RLModules' => array(),
        'RLStyleModules' => array(),
);
## Add 'Contact us' on footer.
$wgHooks['SkinTemplateOutputPageBeforeExec'][] = function( $skin, &$template ) {
        $contactLink = Html::element( 'a', [ 'href' => "https://shinycolors.wiki/wiki/Special:Contact" ],
                "Contact us" );
        $template->set( 'contact', $contactLink );
        $template->data['footerlinks']['places'][] = 'contact';
        return true;
};

## Fix Trust issue with overrided sender email.
$wgHooks['EmailUser'][] = function ( &$address, &$from, &$subject, &$text, &$error ) {
        global $wgPasswordSender;

        if ($form->address != $wgPasswordSender) {
                $text = 'E-Mail From ' . $from->address . "\n\n" . $text;
                $from->address = $wgPasswordSender;
        }
};


## Trigger Captcha on request
$wgCaptchaTriggers['contactpage'] = true;

# CSS
wfLoadExtension( 'CSS' );

# User Merge and Delete
wfLoadExtension( 'UserMerge' );
$wgGroupPermissions['bureaucrat']['usermerge'] = true;

# Rename User
wfLoadExtension( 'Renameuser' );

# WikiSEO
wfLoadExtension( 'WikiSEO' );


# Widgets
wfLoadExtension( 'Widgets' );
#require_once "$IP/extensions/Widgets/Widgets.php";

# UploadWizard
#wfLoadExtension( 'UploadWizard' );

# MsUpload
wfLoadExtension( 'MsUpload' );

# TimedMediaHandler
wfLoadExtension( 'TimedMediaHandler' );
$wgFFmpegLocation = '/usr/bin/ffmpeg';
$wgFFprobeLocation = '/usr/bin/ffprobe';
$wgTmhWebPlayer = 'videojs';
$wgEnableTranscode = true;
$wgTranscodeBackgroundTimeLimit = 3600 * 8;
$wgTmhEnableMp4Uploads = true;
$wgEnabledTranscodeSet = [
        '160p.webm' => true,
        '240p.webm' => true,
        '360p.webm' => true,
        '480p.webm' => true,
        '720p.webm' => true
];

#$wgReadOnly = 'We\'re updating wiki and system software for better user experiences. <b>ETA is 0:00 UTC</b>';

# Elastic Search
wfLoadExtension( 'Elastica' );
wfLoadExtension( 'CirrusSearch' );
#require_once "$IP/extensions/CirrusSearch/CirrusSearch.php";
#$wgDisableSearchUpdate = true;
#$wgSearchType = 'SMWSearch';

# Related Articles that depends CirrusSearch(Elasticearch)
wfLoadExtension( 'RelatedArticles' );
$wgRelatedArticlesUseCirrusSearch = true;
#$wgRelatedArticlesOnlyUseCirrusSearch = true;
$wgRelatedArticlesFooterWhitelistedSkins = array( 'vector', 'minerva' );

# Math/Tex
wfLoadExtension( 'Math' );
#$wgMathFullRestbaseURL= 'https://en.wikipedia.org/api/rest_';
$wgDefaultUserOptions['math'] = 'mathml';
#$wgMathMathMLUrl = 'http://127.0.0.1:10042/';
$wgMathoidCli = ['/opt/wiki/mathoid/cli.js', '-c', '/opt/wiki/mathoid/config.dev.yaml'];
#$wgMathValidModes[] = 'png';
#$wgDefaultUserOptions['math'] = 'png';
#wfLoadExtension( 'SimpleMathJax' );

# SVG Support
$wgFileExtensions[] = 'svg';
$wgAllowTitlesInSVG = true;
$wgSVGConverter = 'rsvg';

# Image upload by url for sysop
$wgAllowCopyUploads = true;
$wgCopyUploadsFromSpecialUpload = true;
$wgGroupPermissions['sysop']['upload_by_url'] = true;

# Graph
wfLoadExtension( 'JsonConfig' ); # dependency
wfLoadExtension( 'Graph' );

# TrustedXFF
#require_once('$IP/extensions/TrustedXFF/TrustedXFF.php');
wfLoadExtension( 'TrustedXFF' );

# Advanced Search
wfLoadExtension( 'AdvancedSearch' );

# Cite This Page
wfLoadExtension( 'CiteThisPage' );

# Lua Script Support
wfLoadExtension( 'Scribunto' );
$wgScribuntoDefaultEngine = 'luastandalone';
#$wgScribuntoDefaultEngine = 'luasandbox';

# Poem
# wfLoadExtension( 'Poem' );

# Contribution Scores
require_once "$IP/extensions/ContributionScores/ContributionScores.php";
$wgContribScoreIgnoreBots = true;
$wgContribScoreIgnoreBlockedUsers = true;
$wgContribScoresUseRealName = false;
$wgContribScoreDisableCache = false;
$wgContribScoreReports = array(
        array(7,50),
        array(30,50),
        array(0,50));

# Template Wizard (+ Data)
wfLoadExtension( 'TemplateData' );
wfLoadExtension( 'TemplateWizard' );

# Article problem
$wgMaxArticleSize = 4096;

# SmiteSpam
require_once "$IP/extensions/SmiteSpam/SmiteSpam.php";
$wgGroupPermissions['bureaucrat']['smitespam'] = true;
$wgSmiteSpamIgnoreSmallPages = false;

# DNS Block
$wgEnableDnsBlacklist = true;

# Semantic Mediawiki and their extensions
enableSemantics( 'shinycolors.wiki' );
wfLoadExtension( 'SemanticResultFormats' );
$smwgMainCacheType = 'redis';
$smwgQueryResultCacheType = 'redis';
$smwgNamespace = 'https://shinycolors.wiki/id/';
$smwgDefaultStore = 'SMWElasticStore';
$smwgElasticsearchEndpoints = [ 'localhost:9200' ];
$smwgDVFeatures = $smwgDVFeatures | SMW_DV_PVUC;
$wgSearchType = 'SMWSearch';
use CirrusSearch\CirrusSearch;
$smwgFallbackSearchType = function() {
        return new CirrusSearch();
};

# Secure Link Fixer
wfLoadExtension( 'SecureLinkFixer' );

# VisualEditor
wfLoadExtension( 'VisualEditor' );

# AWS
wfLoadExtension( 'AWS' );


# Load Secret.
require_once './secret.php';


#$wgShowExceptionDetails = true;