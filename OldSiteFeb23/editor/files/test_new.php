<?php
require_once 'library/security/_secure.inc.php';
require_once 'files/_file_builder.class.php';

$builder = new FileBuilder();

$builder->setLocation('about/');
$builder->setFilename('test.htm');
$builder->setTitle('Page Title');
$builder->setDescription('Page Description');
$builder->setKeywords('Page Keywords');
$builder->setContent('<p>Content  goes here!</p>');

$builder->buildPage();

?>