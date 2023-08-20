<?php
require 'library/security/_secure.inc.php';

$pdo = create_pdo();

$select = $pdo->prepare("SELECT * FROM video_links ORDER BY title ASC");

if($select->execute()){
    $video_links = $select->fetchAll(PDO::FETCH_ASSOC);
}

header("Content-Type: application/json");
echo json_encode($video_links);
