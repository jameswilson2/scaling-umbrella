<?php

define('SITE_NAME', 'Kencomp Internet Ltd');

define('CONTACT_EMAIL', 'webenquiries@kencomp.net');
define('NEWSLETTER_EMAIL', 'webenquiries@kencomp.net');
define('TEST_EMAIL', 'webenquiries@kencomp.net');

define('EDITABLE_ROOT', '/home/sites/kencomp.net/public_html/');
define('WEB_ROOT', 'http://www.kencomp.net/');
define('CSS', WEB_ROOT.'presentation/screen.css');
define('TEMPLATE_DIR', EDITABLE_ROOT . '/editor/templates');
define('TEMPLATE_CACHE_DIR', EDITABLE_ROOT . '/editor/templates_c');

// database for dynamic elements
define('DB_SERVER','localhost');
define('DB_USER','cl58-c152micro');
define('DB_PASSWORD','news124itedata112423');
define('DB_DATABASE','cl58-c152micro');

define('SMTP_HOSTNAME', 'mail.kencomp.net');
define('SMTP_PORT', 25);
define('SMTP_USERNAME', "webenquiries@kencomp.net");
define('SMTP_PASSWORD', "kencomp3275");

$menus = array(
	'files',
	'imagery',
	'coverage',
	'faq',
	'comments',
	'gallery',
	'points-of-interest',
	'enquiries',
	'newsletter',
	'news',
	'upload',
	'video_links',
	'core');

// folders with no editor access
$disallowed_folders = array(
	'editor/',
	'behaviour/',
	'behavior/',
	'presentation/',
	'backup/',
	'downloads/',
	'faq/',
	'flash/',
	'gallery/',
	'newsletter/',
	'generator/',
	'videos/',
	'images/',
	'news/',
	'search/',
	'highslide/',
	'comments/',
	'_bak/',
	'deleted/'
	);


// files with no editor access
$disallowed_files = array(
	'index2.html',
	'google503b34205271f687.html',
	'TMPr5nwuuowva.htm'
	);

define('HIDE_DISALLOWED_FILES', TRUE);

// edit only files
$no_delete = array(
	'comments/thankyou.htm',
	'enquiries/thankyou.htm'
);

// edit only folders
$no_delete_folders = array(

);


$disallowed_includes = array(
	'_flash.inc',
	'_extra_css.inc',
	'_extra_jslibs.inc'
);

define('HIDE_DISALLOWED_INCLUDES', TRUE);

?>