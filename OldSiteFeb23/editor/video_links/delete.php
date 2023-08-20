<?php
require 'library/security/_secure.inc.php';

$pdo = create_pdo();

$select = $pdo->prepare("SELECT * FROM video_links WHERE id = :id");
$select->bindValue(":id", $_GET["id"]);

if(!$select->execute()){
    die("error");
}

$video_link = $select->fetch(PDO::FETCH_ASSOC);

if(!$video_link){
    die("not found");
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $delete = $pdo->prepare("DELETE FROM video_links WHERE id = :id");
    $delete->bindValue(":id", $_GET["id"]);
    if($delete->execute()){
        $redirect = WEB_ROOT . "editor/video_links/index.php";
        header("location: $redirect");
        exit;
    }
}

$page = new Page($menus);
$header = $page->getHeader();
$footer = $page->getFooter();
echo $header;
?>
<h1>Delete Video Link</h1>

<table border="0" cellpadding="0" cellspacing="0" class="table">
    <tr class="row">
        <td><strong>Title</td>
        <td><?php echo htmlentities($video_link["title"], ENT_COMPAT, "UTF-8");?></td>
    </tr>
    
    <tr class="row">
        <td><strong>Url</td>
        <td><?php echo htmlentities($video_link["url"], ENT_COMPAT, "UTF-8");?></td>
    </tr>
</table>

<form method="post">
    <input type="submit" value="Delete" />
</form>

<?php
echo $footer;
