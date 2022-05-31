<?php

$wgDBtype = 'mysql';
$wgDBserver = 'mariadb';
$wgDBname = 'shinywiki';
$wgDBuser = 'shinywiki';
$wgDBpassword = 'shinywiki';

$wgSecretKey = 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa';

$wgUpgradeKey = 'bbbbbbbbbbbbbbbb';

$wgSentryDsn = 'https://aaa:bbb@sentry.io/111111';

$wgAwsSesCredentials = [
    'key' => 'key',
    'secret' => 'sec',
    'token' => false
];
$wgAwsSesRegion = 'us-west-1';

$wgHCaptchaSiteKey = '';
$wgHCaptchaSecretKey = '';

$wgGTagAnalyticsId = '';

$wgSFSAPIKey = '';

$wgDiscordWebhookURL = [ '' ];

$wgAWSCredentials = [
    'key' => 'key',
    'secret' => 'sec',
    'token' => false
];
$wgAWSRegion = '';
$wgAWSBucketName = '';
$wgAWSRepoHashLevels = '2'; # migrate existing images
$wgAWSRepoDeletedHashLevels = '3'; # migrate existing images
$wgAWSBucketDomain = $wgAWSBucketName;
#$wgFileBackends['s3']['endpoint'] = '';

$wgCrowdSecAPIKey = '';
$wgCrowdSecReportOnly = false;

$wgSpamBlacklistFiles = array(
    "https://meta.wikimedia.org/wiki/Spam_blacklist",
    "https://meta.wikimedia.org/wiki/MediaWiki:Spam-blacklist",
    "https://en.wikipedia.org/wiki/MediaWiki:Spam-blacklist"
);

$wgEnableDnsBlacklist = false;
$wgDnsBlacklistUrls = array( '' );
$wgDnsBlacklistUrls['create']        = true;
$wgDnsBlacklistUrls['createtalk']    = true;
$wgDnsBlacklistUrls['addurl']        = true;
$wgDnsBlacklistUrls['createaccount'] = true;
$wgDnsBlacklistUrls['edit']          = true;