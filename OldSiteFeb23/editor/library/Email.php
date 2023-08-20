<?php

class Email{
    
    private $pdo;
    private $id;
    private $from_name;
    private $from_address;
    private $recipients = array();
    private $subject;
    private $content_type = "text/plain";
    private $body = "";
    private $attachments = array();
    
    function __construct(PDO $pdo){
        $this->pdo = $pdo;
    }
    
    function setFrom($from_address, $from_name){
        $this->from_address = $from_address;
        $this->from_name = $from_name;
    }
    
    function setTo($recipients){
        
        $this->recipients = array();
        
        foreach($recipients as $address => $name){
                
            if(is_int($address)){
                $address = $name;
                $name = null;
            }
            
            $this->recipients[] = array(
                "to_address" => $address,
                "to_name" => $name,
                "status" => "unsent",
                "tries" => 0
            );
        }
    }
    
    function setSubject($subject){
        $this->subject = $subject;
    }
    
    function setContentType($content_type){
        $this->content_type = $content_type;
    }
    
    function setBody($body){
        $this->body = $body;    
    }
    
    function addAttachment($filename, $content_type = null){
        $this->file_attachments[] = array($filename, $content_type);
    }
    
    function queue(){
        
        if($this->id !== null){
            return;
        }
        
        $pdo = $this->pdo;
        
        $pdo->exec("START TRANSACTION");
        
        $insert = $pdo->prepare("
            INSERT INTO email (from_address, from_name, subject, content_type, body, created_at) 
            VALUES(:from_address, :from_name, :subject, :content_type, :body, NOW())
        ");
        
        $insert->bindValue(":from_address", $this->from_address);
        $insert->bindValue(":from_name", $this->from_name);
        $insert->bindValue(":subject", $this->subject);
        $insert->bindValue(":content_type", $this->content_type);
        $insert->bindValue(":body", $this->body);
        
        $id = null;
        
        if($insert->execute()){
            
            $this->id = $pdo->lastInsertId();
            
            $this->queueAttachments();
            $this->queueRecipients();
        }
        
        $pdo->exec("COMMIT");
        
        return $id;
    }
    
    private function update(){
        
        if($this->id === null){
            return;
        }
        
        $pdo = $this->pdo;
        
        $all_sent = true;
        
        foreach($this->recipients as &$recipient){
            
            if(!isset($recipient["id"])){
                continue;
            }
            
            if($recipient["status"] == "sent"){
                
                $delete = $pdo->prepare("DELETE FROM email_recipient WHERE id = :id");
                $delete->bindValue(":id", $recipient["id"], PDO::PARAM_INT);
                $delete->execute();
                
                unset($recipient["id"]);
            }
            else{
                
                $all_sent = false;
                
                $update = $pdo->prepare("
                    UPDATE email_recipient 
                    SET status = :status, error_message = :error_message, tries = :tries
                    WHERE id = :id
                ");
                
                $update->bindValue(":status", $recipient["status"]);
                $update->bindValue(":error_message", $recipient["error_message"]);
                $update->bindValue(":tries", $recipient["tries"]);
                $update->bindValue(":id", $recipient["id"], PDO::PARAM_INT);
                
                $update->execute();
            }
        }
        
        if($all_sent){
            
            $delete = $pdo->prepare("DELETE FROM email_recipient WHERE email_id = :email_id");
            $delete->bindValue(":email_id", $this->id, PDO::PARAM_INT);
            $delete->execute();
            
            $delete = $pdo->prepare("DELETE FROM email_attachment WHERE email_id = :email_id");
            $delete->bindValue(":email_id", $this->id, PDO::PARAM_INT);
            $delete->execute();
            
            $delete = $pdo->prepare("DELETE FROM email WHERE id = :id");
            $delete->bindValue(":id", $this->id, PDO::PARAM_INT);
            $delete->execute();
            
            unset($this->id);
        }
    }
    
    private function queueAttachments(){
        
        if($this->id === null || count($this->attachments) == 0){
            return;
        }
        
        $pdo = $this->pdo;
        
        $sql_value_bindings = array();
        $sql_values = array();
        
        $i = 0;
        foreach($this->attachments as $attachment){
            $sql_values[] = "(:email_id, :filename_$i, :content_type_$i)";
            $sql_value_bindings[":filename_$i"] = $attachment[0];
            $sql_value_bindings[":content_type_$i"] = $attachment[1];
            $i += 1;
        }
        
        $insert = $pdo->prepare("INSERT INTO email_attachment (email_id, filename, content_type) VALUES " . implode(",", $sql_values));
        
        $insert->bindValue(":email_id", $this->id, PDO::PARAM_INT);
        
        foreach($sql_value_bindings as $key => $value){
           $insert->bindValue($key, $value); 
        }
        
        return $insert->execute();
    }
    
    private function queueRecipients(){
        
        if($this->id === null){
            return;
        }
        
        $pdo = $this->pdo;
        
        foreach($this->recipients as &$recipient){
            
            if(isset($recipient["id"])){
                continue;
            }
            
            $insert = $pdo->prepare("INSERT INTO email_recipient (email_id, to_address, to_name) VALUES (:email_id, :to_address, :to_name)");
            
            $insert->bindValue(":email_id", $this->id);
            $insert->bindValue(":to_address", $recipient["to_address"]);
            $insert->bindValue(":to_name", $recipient["to_name"]);
            
            if($insert->execute()){
                $recipient["id"] = $pdo->lastInsertId();   
            }
        }
    }

    function createMessage(){
        
        $message = Swift_Message::newInstance();
        
        $from = array();
        $from[$this->from_address] = $this->from_name;
        $message->setFrom($from);
        
        $message->setSubject($this->subject);
        
        $message->setContentType($this->content_type);
        $message->setBody($this->body);
        
        foreach($this->attachments as $attachment){
            $message->attach(Swift_Attachment::fromPath($attachment[0], $attachment[1]));
        }
        
        return $message;
    }
    
    function send($mailer = null, $queue = true){
        
        if(!$mailer){
            $mailer = self::createSMTPMailer();
        }
        
        $message = $this->createMessage();
        $failure = false;
        
        foreach($this->recipients as &$recipient){
            
            $set_to = array();
            
            if($recipient["to_name"] !== null){
                $set_to[$recipient["to_address"]] = $recipient["to_name"];
            }
            else{
                $set_to[0] = $recipient["to_address"];
            }
            
            $message->setTo($set_to);
            
            try{
                $failures = array();
                if(!$mailer->send($message, $failures)){
                    $recipient["status"] = "error";
                    $recipient["error_message"] = "rejected";
                    $failure = true;
                }
                else{
                   $recipient["status"] = "sent";
                }
            }
            catch(Swift_TransportException $transport_error){
                $recipient["status"] = "error";
                $recipient["error_message"] = $transport_error->getMessage();
                $failure = true;
            }
            
            $recipient["tries"] += 1;
        }
        
        if($failure && $queue && $this->id === NULL){
            $this->queue();
        }
        
        $this->update();
    }
    
    static function loadFromRecipients($pdo, $recipients){
        
        if(count($recipients) == 0){
            return;
        }
        
        $email = new Email($pdo);
        $email->id = $recipients[0]["email_id"];
        
        $select = $pdo->prepare("SELECT * FROM email WHERE id = :id");
        $select->bindValue(":id", $email->id, PDO::PARAM_INT);
        
        if($select->execute()){
            
            $attributes = $select->fetch(PDO::FETCH_ASSOC);
            
            $email->from_name = $attributes["from_name"];
            $email->from_address = $attributes["from_address"];
            $email->subject = $attributes["subject"];
            $email->content_type = $attributes["content_type"];
            $email->body = $attributes["body"];
        }
        else{
            return null;
        }
        
        foreach($recipients as $recipient){
            $email->recipients[] = $recipient;
        }
        
        $select = $pdo->prepare("SELECT * FROM email_attachment WHERE email_id = :id");
        $select->bindValue(":id", $email->id, PDO::PARAM_INT);
        
        if($select->execute()){
            while($attachment = $select->fetch(PDO::FETCH_ASSOC)){
                $email->attachments[] = array($attachment["filename"], $attachment["content_type"]);
            }
        }
        
        return $email;
    }
    
    static function dequeue($pdo, $mailer = null){
        
        if(!$mailer){
            $mailer = self::createSMTPMailer();
        }
        
        $select = $pdo->prepare("SELECT * FROM email_recipient");
        
        if($select->execute()){
            
            $recipients = $select->fetchAll(PDO::FETCH_ASSOC);
            
            $recipients_per_email = array();
            
            foreach($recipients as $recipient){
                $recipients_per_email[$recipient["email_id"]][] = $recipient;
            }
            
            foreach($recipients_per_email as $email_recipients){
                $email = self::loadFromRecipients($pdo, $email_recipients);
                if($email){
                    $email->send($mailer);
                }
            }
        }
    }
    
    static function createSMTPMailer($settings = array()){
        
        $hostname = (isset($settings["hostname"]) ? $settings["hostname"] : SMTP_HOSTNAME);
        $port = (isset($settings["port"]) ? $settings["port"] : SMTP_PORT);
        $username = (isset($settings["username"]) ? $settings["username"] : SMTP_USERNAME);
        $password = (isset($settings["password"]) ? $settings["password"] : SMTP_PASSWORD);
        
        $transport = Swift_SmtpTransport::newInstance($hostname, $port);
        $transport->setUsername($username);
        $transport->setPassword($password);
        
        $mailer = Swift_Mailer::newInstance($transport);
        
        return $mailer;
    }
}
