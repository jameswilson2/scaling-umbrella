<?php
require_once 'library/FileExtensions.php';
require_once 'library/FilesystemUtils.php';

class FileUpload{
	
	private $form_field_name;
	private $expected_content_type;
	private $data;
	private $valid;
	
	public function __construct($form_field_name, $expected_content_type = ''){
		
		$this->form_field_name = $form_field_name;
		$this->expected_content_type = explode("/", $expected_content_type);
		$this->data = $_FILES[$form_field_name];
		
		$this->valid = $this->data && $this->data['error'] == UPLOAD_ERR_OK && $this->isExpectedContentType();
	}
	
	private function isExpectedContentType(){
		$upload_content_type = explode("/", $this->data['type']);
		return $this->expected_content_type[0] == "" || (
				$upload_content_type[0] == $this->expected_content_type[0] && 
				(!$this->expected_content_type[1] || $upload_content_type[1] == $this->expected_content_type[1])
			);
	}
	
	public function isValidUpload(){
		return $this->valid;
	}
	
	public function getContentType(){
		return $this->data['type'];
	}
	
	public function getFileExtension(){
		return getFileExtensionFromContentType($this->data['type']);
	}
	
	public function randomizeName(){
		$file_ext = $this->getFileExtension();
		$this->data['name'] = md5(mt_rand(1, 4294967296) . time()) . "." . $file_ext;
	}
	
	public function copy($destination){
		$source = $this->data['tmp_name'];
		if($this->isValidUpload() && is_uploaded_file($source)){
			return copy($source, $destination);
		}
		return false;
	}
	
	public function copyTo($destination_dir){
		if($this->isValidUpload()){
			
			$destination = unique_filename(normalize_filename($this->getName(), EDITABLE_ROOT . $destination_dir));
			
			if($this->copy($destination)){
				
				$destination_info = pathinfo($destination);
				$name = $destination_info["basename"];
				
				return array(
					"name" => $destination_info["basename"],
					"filename" => $destination,
					"directory" => $destination_dir,
					"url" => $destination_dir . $name
				);
			}
		}
		return false;
	}
	
	public static function getEditorTempPath(){
		return EDITABLE_ROOT . "editor/temp/";
	}
	
	public static function getEditorTempWebPath(){
		return WEB_ROOT . "editor/temp/";
	}
	
	public function getTempFilename(){
		return $this->data['tmp_name'];
	}
	
	public function getName(){
		return $this->data['name'];
	}
	
	public static function hasUploadedFile($field_name){
		$upload = @$_FILES[$field_name];
		if($upload){
			if($upload['error'] === UPLOAD_ERR_OK){
				return true;
			}
		}
		return false;
	}
}
