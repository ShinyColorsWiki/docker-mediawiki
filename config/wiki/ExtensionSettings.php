<?php

# Enabled skins.
wfLoadSkin( 'MonoBook' );
wfLoadSkin( 'Timeless' );
wfLoadSkin( 'Vector' );

# Enabled extensions. Most of the extensions are enabled by adding
wfLoadExtension( 'SpamBlacklist' );
wfLoadExtension( 'TitleBlacklist' );

# Sentry (Load Eailier for check error)
wfLoadExtension( 'Sentry' );

# Mailer
wfLoadExtension( 'AwsSesMailer' );

# DismissableSiteNotice
wfLoadExtension( 'DismissableSiteNotice' ); # for SiteNotice
$wgMajorSiteNoticeID = 0; # Hey, Auto update enabled.
$wgDismissableSiteNoticeForAnons = true;

# Interwiki.
wfLoadExtension( 'Interwiki' ); # for Interwiki
$wgGroupPermissions['sysop']['interwiki'] = true;

# Captcha
wfLoadExtensions([ 'ConfirmEdit', 'ConfirmEdit/hCaptcha' ]);

# Prevent spam
#$wgGroupPermissions['emailconfirmed']['skipcaptcha'] = true;
#$ceAllowConfirmedEmail = true;
$wgGroupPermissions['autoconfirmed']['skipcaptcha'] = true;
$wgGroupPermissions['autoconfirmed']['crowdsec-bypass'] = true;

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
wfLoadExtension( 'CodeEditor' );

# CheckUser
wfLoadExtension( 'CheckUser' );
$wgGroupPermissions['sysop']['checkuser'] = true;
$wgGroupPermissions['sysop']['checkuser-log'] = true;

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

# Variables and Loops (Disabled due to deperecation of MW hooks)
wfLoadExtension( 'Variables' );
wfLoadExtension( 'Loops' );
$egVariablesDisabledFunctions = [ 'var_final' ];

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

# Discord
wfLoadExtension( 'Discord' );

# Contact Page
wfLoadExtension( 'ContactPage' );
$wgContactConfig['default'] = array(
        'RecipientUser' => 'ContactUser',
        'SenderName' => 'Contact Form on ' . $wgSitename,
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

# MobileFrontend & Minerva(Neue)
#wfLoadSkin( 'MinervaNeue' );
#wfLoadExtension( 'MobileFrontend' );
#$wgMobileUrlTemplate = 'm.shinycolors.wiki';
#$wgMFMobileHeader = 'X-Use-Mobile';
#$wgMFAutodetectMobileView = false; # will do sometime.

# CSS
wfLoadExtension( 'CSS' );

# User Merge and Delete
wfLoadExtension( 'UserMerge' );
$wgGroupPermissions['bureaucrat']['usermerge'] = true;

# Rename User
wfLoadExtension( 'Renameuser' );

# WikiSEO
wfLoadExtension( 'WikiSEO' );

# Cargo
wfLoadExtension( 'Cargo' );
$wgCargoDBRowFormat = 'COMPRESSED';
$wgCargoHideNamespaceName[] = NS_USER;

# Widgets
wfLoadExtension( 'Widgets' );
#require_once "$IP/extensions/Widgets/Widgets.php";

# UploadWizard
#wfLoadExtension( 'UploadWizard' );

#SimpleBatchUpload
wfLoadExtension( 'SimpleBatchUpload' );

# MsUpload
#wfLoadExtension( 'MsUpload' );

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

# Elastic Search
wfLoadExtension( 'Elastica' );
wfLoadExtension( 'CirrusSearch' );
$wgCirrusSearchClusters = [
    'default' => [ 'elasticsearch' ],
];

# Related Articles that depends CirrusSearch(Elasticearch)
wfLoadExtension( 'RelatedArticles' );
$wgRelatedArticlesUseCirrusSearch = true;
#$wgRelatedArticlesOnlyUseCirrusSearch = true;
$wgRelatedArticlesFooterWhitelistedSkins = array( 'vector', 'minerva' );

# Math/Tex
wfLoadExtension( 'Math' );
$wgDefaultUserOptions['math'] = 'mathml';
$wgMathFullRestbaseURL = 'https://wikimedia.org/api/rest_';
$wgMathMathMLUrl = 'https://mathoid-beta.wmflabs.org';

# Graph
wfLoadExtension( 'JsonConfig' ); # dependency
wfLoadExtension( 'Graph' );

# TrustedXFF
wfLoadExtension( 'TrustedXFF' );

# Advanced Search
wfLoadExtension( 'AdvancedSearch' );

# Cite This Page
wfLoadExtension( 'CiteThisPage' );

# Lua Script Support
wfLoadExtension( 'Scribunto' );
$wgScribuntoDefaultEngine = 'luasandbox';
#$wgScribuntoEngineConf['luastandalone']['luaPath'] = '/usr/bin/lua';

# Contribution Scores
wfLoadExtension( 'ContributionScores' );
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
wfLoadExtension( 'SmiteSpam' );
$wgGroupPermissions['bureaucrat']['smitespam'] = true;
$wgSmiteSpamIgnoreSmallPages = false;

# Semantic Mediawiki and their extensions
wfLoadExtension( 'SemanticMediaWiki' );
enableSemantics( 'shinycolors.wiki' );
wfLoadExtension( 'SemanticResultFormats' );
wfLoadExtension( 'SemanticScribunto' );
$smwgMainCacheType = 'redis';
$smwgQueryResultCacheType = 'redis';
$smwgNamespace = 'https://shinycolors.wiki/id/';
//$smwgDefaultStore = 'SMWElasticStore';
//$smwgElasticsearchEndpoints = [ 'elasticsearch:9200' ];
$smwgDVFeatures = $smwgDVFeatures | SMW_DV_PVUC;
$wgSearchType = 'SMWSearch';
use CirrusSearch\CirrusSearch;
$smwgFallbackSearchType = function() {
        return new CirrusSearch();
};

# Secure Link Fixer
wfLoadExtension( 'SecureLinkFixer' );

# VisualEditor and Parsoid
wfLoadExtension( 'VisualEditor' );

# AWS
wfLoadExtension( 'AWS' );

# DarkMode - Disabled due to site-wide css corruption
#wfLoadExtension( 'DarkMode' );

# CrowdSec
wfLoadExtension( 'CrowdSec' );

# Tabs
wfLoadExtension( 'Tabs' );