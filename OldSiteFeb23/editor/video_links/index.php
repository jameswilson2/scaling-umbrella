<?php
require 'library/security/_secure.inc.php';

$url = Url::parseFromRequest();

$table = new TableView("video_links", array(
    array("name" => "title", "orderable" => true),
    array("name" => "url"),
    array("render" => "render_control_column")
));
$table->loadViewStateFromUrl($url);

function render_control_column($row){
    $id_url_safe = urlencode($row["id"]);
    return "<a href=\"video_links/edit.php?id=$id_url_safe\">Edit</a> | <a href=\"video_links/delete.php?id=$id_url_safe\">Delete</a>";
}

$table_html = $table->render();

$content = <<<EOD
<h1>Video Links</h1>
<div id="newitems">
    <p><strong><img src="presentation/add.png" alt="" style="vertical-align:middle;" /> <a href="video_links/edit.php">Add Video Link</a></strong></p>
</div>
$table_html
EOD;

$page = new Page($menus);
$header = $page->getHeader();
$footer = $page->getFooter();
echo $header;
echo $content;
echo $footer;
