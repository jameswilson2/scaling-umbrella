<?php
require_once "library/config/_editor_config.php";
require_once "library/html/HtmlElement.class.php";
require_once "library/html/HtmlLiteral.class.php";
require_once "library/FormData.class.php";
require_once "library/FieldRendering.php";
require_once "library/FieldSQLRendering.php";

class Form{
	
	protected $fields = array();
	protected $form_data;

	private $render_submit_value = "Submit";
	
	protected $db_table = "";
	protected $db_column_prefix = "";
	protected $db_primary_key;
	
	protected $send_admin_email = false;
	protected $admin_email_addresses = array();
	protected $admin_email_subject;
	protected $admin_email_from = CONTACT_EMAIL;
	protected $admin_email_from_use_field;
	
	public function __construct(){
	
		$this->form_data = new FormData(get_class($this));
		
		if(!$this->admin_email_subject){
			$this->admin_email_subject = SITE_NAME . " website form submission notification";
		}
		
		$this->addAdminEmailAddress(CONTACT_EMAIL);
	}
	
	public function get($url){
	
		$form = new HtmlElement("form");
		$form->setAttribute("method", "POST");
		$form->setAttribute("action", $url);
		
		$form->addChild(new HtmlLiteral(renderFieldsArray($this->fields, $this->form_data)));
		
		$submit_container = new HtmlElement("div");
		$submit_container->addClass("form-buttons");
		
		$submit = new HtmlElement("input");
		$submit->setAttribute("type", "submit");
		$submit->addClass("input-submit");
		$submit->setAttribute("value", $this->render_submit_value);
		
		$submit_container->addChild($submit);
		$form->addChild($submit_container);
		
		$render = $form->toString();
		
		/*if($this->){
			$this->form_data->destroy();
		}*/
		
		return $render;
	}
	
	public function post(){
		
		saveFieldsFromPost($this->fields, $this->form_data);
		
		$has_errors = $this->form_data->hasErrors();
		
		if(!$has_errors){
			
			$this->insertDataIntoDatabase();
			$this->sendNotificationEmails();
			
			$this->form_data->destroy();
		}
		else{
			$this->form_data->save();
		}
		
		return !$has_errors;
	}
	
	public function getFormData(){
		return $this->form_data;
	}
	
	public function saveFormData(){
		$this->form_data->save();
	}
	
	public function setRenderSubmitValue($value){
		$this->render_submit_value = $value;
	}
	
	public function setDatabaseTable($table){
		$this->db_table = $table;
	}
	
	public function setDatabaseColumnPrefix($column_prefix){
		$this->db_column_prefix = $column_prefix;
	}
	
	protected function insertDataIntoDatabase($db_connection = null){
		
		if($this->db_primary_key){
			
			$pk_column = "{$this->db_column_prefix}_{$this->db_primary_key}";
			$pk_value = safeaddslashes($this->form_data->getField($this->db_primary_key));
			$sql = "SELECT $pk_column FROM $this->db_table WHERE $pk_column = '$pk_value'";
			
			if($db_connection){
				
			}
			else{
				$result = getQuery($sql);
				if(mysql_num_rows($result) > 0){
					return;
				}
			}
		}
		
		$column_assignments = renderSQLColumnAssignments($this->db_column_prefix, $this->fields, $this->form_data);
		$column_assignments = implode(",\n", $column_assignments);
		
		$sql = "INSERT INTO $this->db_table SET $column_assignments";
		
		if($db_connection){
			$db_connection->exec($sql);
		}
		else{
			getQuery($sql);
		}
	}
	
	public function getUntypedSchemaSQL(){
		
		$id_column = "{$this->db_column_prefix}_id";
		
		$columns = array();
		$columns[] = "`$id_column` int(11) NOT NULL AUTO_INCREMENT";
		$columns = array_merge($columns, renderSQLColumnDefinitions($this->db_column_prefix, $this->fields));
		$columns[] = "PRIMARY KEY (`$id_column`)";
		
		$columns = implode(",\n", $columns);
		
		$sql = "CREATE TABLE IF NOT EXISTS `$this->db_table` (\n$columns) ENGINE=MyISAM  DEFAULT CHARSET=latin1";
		return $sql;
	}
	
	public function setAdminEmailSubject($subject){
		$this->admin_email_subject = $subject;
	}
	
	public function addAdminEmailAddress($email){
		$this->admin_email_addresses[] = $email;
	}
	
	protected function sendNotificationEmails(){
		
		if(!$this->send_admin_email){
			return;
		}
		
		foreach($this->admin_email_addresses as $email_address){
			$this->sendNotificationEmail($email_address);
		}
	}
	
	private function sendNotificationEmail($email){
	
		$fields = array();
		
		foreach($this->fields as $field){
			switch($field["type"]){
				case "captcha":
				case "internal":
				case "hidden":
					continue 2;
			}
			$field_name = @$field["label"];
			$field_value = $this->form_data->getField($field["name"]);
			$fields[] = "$field_name $field_value";
		}
		
		$fields[] = "";
		
		$now = date("l jS F Y \a\\t H:i \h\\r\s");
		$fields[] = "Date: $now";
		
		$fields[] = "Client IP: {$_SERVER['REMOTE_ADDR']}";
	
		$email_content = implode("\n", $fields);
		
		$from = $this->admin_email_from;
		if($this->admin_email_from_use_field){
			$from = $this->form_data->getField($this->admin_email_from_use_field);
		}
		
		$header = "From $from";
		
		mail($email, $this->admin_email_subject, $email_content, $headers);
	}
}
