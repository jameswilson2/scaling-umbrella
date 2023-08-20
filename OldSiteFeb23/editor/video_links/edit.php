<?php
require 'library/security/_secure.inc.php';

$url = Url::parseFromRequest();
$pdo = create_pdo();

if(isset($_GET["id"])){
    
    $select = $pdo->prepare("SELECT * FROM video_links WHERE id = :id");
    $select->bindValue(":id", $_GET["id"]);
    
    if($select->execute()){
        $video_link = $select->fetch(PDO::FETCH_ASSOC);
    }
    
    if(!$video_link){
        echo "video link not found";
        exit;
    }
    
    $title = "Edit Video Link";
}
else{
    $title = "Add Video Link";
}

$form = new Form;
$form->addField(array("type" => "text", "name" => "title"));
$form->addField(array("type" => "text", "name" => "url"));
$form->addSubmitField($title);

if(isset($video_link)){
    $form->loadFromStorage($video_link);
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    if($form->loadFromSubmit($_POST)){
        
        $values = $form->getStorageValues();
        
        if(isset($video_link)){
            
            $save = $pdo->prepare("
                UPDATE video_links 
                SET title = :title, url = :url
                WHERE id = :id
            ");
            
            $save->bindValue(":id", $_GET["id"]);
        }
        else{
            $save = $pdo->prepare("
                INSERT INTO video_links (title, url) VALUES (:title, :url)
            ");
        }
        
        $save->bindValue(":title", $values["title"]);
        $save->bindValue(":url", $values["url"]);
        
        if($save->execute()){
            
            if(!isset($video_link)){
                $url->setQueryVar("id", $pdo->lastInsertId());
            }
            
            header("location: " . $url->render());
            exit;
        }
    }
}

$page = new Page($menus);
$header = $page->getHeader();
$footer = $page->getFooter();
echo $header;
?>
<h1><?php echo $title;?></h1>

<div class="form-container">
<?php echo $form->createFieldElement()->render();?>
</div>

<?php
echo $footer;