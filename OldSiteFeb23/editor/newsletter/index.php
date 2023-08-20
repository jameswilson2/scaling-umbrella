<?php
require_once 'library/security/_secure.inc.php';
require_once 'library/_page.class.php';
require_once 'library/_extras_dbconn.inc.php';
require_once 'newsletter/_newsletters.class.php';

$table = new NewsletterTable();

$content = $table->getTable();

$page = new Page($menus);

$header = $page->getHeader();
$footer = $page->getFooter();

echo $header; ?>
<?php echo $content; ?>

<p><a href="newsletter/edit.php?new=1">Create a new Newsletter</a></p>

<script type="text/javascript">

    var sendingNewsletter = false;
    
    function confirmSendNewsletter(button){
        
        if(sendingNewsletter){
            alert("A newsletter is being sent to subscribers. Please be patient, this could take several minutes to complete...");  
            return false; 
        }
        
        return confirm("Are you sure you want to send this to all your subscribers?");
    }
    
    function sendNewsletterOnSubmit(){
        
        if(sendingNewsletter){
            return false;
        }
        
        sendingNewsletter = true;
    }
    
</script>
<?php echo $footer; ?>
