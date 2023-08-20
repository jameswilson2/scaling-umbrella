<?php

class Form_ImageField extends Form_FieldBase{
    
    private $use_files_global = false;
    private $randomize_filename = false;
    private $view_stored_image = false;
    private $view_stored_image_dims = array(null, 64);
    private $destination;
    private $base_uri;
    private $after_store_callback;

    private $stored_filename;
    
    function setDestination($destination){
        
        $this->destination = $destination;

        if($destination[strlen($destination) - 1] != "/"){
            $this->destination .= "/";
        }
    }

    function setBaseUri($base_uri){
        $this->base_uri = $base_uri;
    }

    function setRandomizeFilename($randomize_filename){
        $this->randomize_filename = $randomize_filename;
    }

    function setUseFilesGlobal($use_files_global){
        $this->use_files_global = $use_files_global;
    }

    function setViewStoredImage($view_stored_image){
        $this->view_stored_image = $view_stored_image;
    }

    function setViewStoredImageDimensions($view_stored_image_dims){
        $this->view_stored_image_dims = $view_stored_image_dims;
    }

    function setAfterStoreCallback($after_store_callback){
        $this->after_store_callback = $after_store_callback;
    }

    function isLargeData(){
        return true;
    }

    function getStorageKeys(){
        return array($this->getName());
    }

    function getStorageValues(){
        return array($this->getName() => $this->stored_filename);    
    }

    function loadFromStorage($data){
        
        $name = $this->getName();

        if(isset($data[$name])){
            $filename = $data[$name];
            $this->stored_filename = $filename;
        }

        return true;
    }

    function loadFromSubmit($data){
        
        $name = $this->getName();

        if(isset($data[$name])){
            $file = $data[$name];
        }
        else if($this->use_files_global && isset($_FILES[$name])){
            $file = $_FILES[$name];
        }
        else{
            $file = array(
                "error" => UPLOAD_ERR_NO_FILE
            );
        }
        
        if($file["error"] == UPLOAD_ERR_NO_FILE){
            
            if($this->isOptional() || $this->stored_filename){
                return true;
            }
            
            $this->setErrorMessage("this is a required field");
            return false;
        }
        
        if($file["size"] == 0){
            $this->setErrorMessage("file size of 0 bytes is disallowed");
            return false;
        }
        
        if($file["error"] != UPLOAD_ERR_OK){
            $this->setErrorMessage("failed to upload image: " . self::getFileUploadErrorMessage($file["error"]));
            return false;
        }

        if(strpos($file["type"], "image/") !== 0){
            $this->setErrorMessage("require an image file");
            return false;
        }

        if(!file_exists($this->destination) || !is_dir($this->destination)){
            $this->setErrorMessage("server error: destination directory not found");
            return false;
        }

        $temp_filename = $file["tmp_name"];

        if(!is_uploaded_file($temp_filename)){
            $this->setErrorMessage("upload error");
            return false;
        }
        
        $client_filename = $file["name"];

        if($this->randomize_filename){
        
            $ext = self::getFileExtension($client_filename);

            $client_filename = uniqid("img_", true) . $ext;
            
            while(file_exists($this->resolveFilename($client_filename))){
                $client_filename = uniqid("event_", true) . $ext;
            }
        }
        
        $destination_filename = $this->resolveFilename($client_filename);

        if($this->stored_filename){
            $delete_existing = true;
            $stored_destination_filename = $this->resolveFilename($this->stored_filename);
        }
        else{
            $delete_existing = false;
        }

        if(file_exists($destination_filename)){
            // Permit the uploader to replace the existing file
            if(isset($stored_destination_filename) && $destination_filename == $stored_destination_filename){
                $delete_existing = false;
            }
            else{
                $this->setErrorMessage("filename already in use");
                return false;
            }
        }

        if(!@copy($temp_filename, $destination_filename)){
            $this->setErrorMessage("server error: could not put file in directory");
            return false;
        }

        $destination_filename_pathinfo = pathinfo($destination_filename);

        $this->stored_filename = $destination_filename_pathinfo["basename"];

        if($this->after_store_callback){
            call_user_func($this->after_store_callback, $destination_filename);
        }

        if($delete_existing){
            unlink($existing_destination_filename);
        }

        return true;
    }

    private function resolveFilename($filename){
        $filename = str_replace("/", "_", $filename);
        return $this->destination . $filename;
    }

    static function getFileExtension($filename){
        $matches = array();
        if(preg_match("/\\.([^.]+)$/", $filename, $matches)){
            return "." . $matches[1];
        }
        return "";
    }

    static function getFileUploadErrorMessage($error_code){
        switch ($error_code) {
            case UPLOAD_ERR_INI_SIZE:
                return 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
            case UPLOAD_ERR_FORM_SIZE:
                return 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
            case UPLOAD_ERR_PARTIAL:
                return 'The uploaded file was only partially uploaded';
            case UPLOAD_ERR_NO_FILE:
                return 'No file was uploaded';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Missing a temporary folder';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Failed to write file to disk';
            case UPLOAD_ERR_EXTENSION:
                return 'File upload stopped by extension';
            default:
                return 'Unknown upload error';
        }
    }

    function createWidgetElement(){

        $output = array();

        $input = new Html_InputElement("file");
        $this->setWidgetAttributes($input);

        $output[] = $input;

        if($this->view_stored_image && $this->stored_filename && file_exists($this->resolveFilename($this->stored_filename))){
            
            $image_url = $this->base_uri . $this->stored_filename;
            
            $image = new Html_Element("img");
            $image->setAttribute("src", $image_url);
            $image->addClass("image-preview");
            
            $view_stored_image_dims = $this->view_stored_image_dims;
            
            if($view_stored_image_dims[0] !== null){
                $image->setAttribute("width", $view_stored_image_dims[0]);
            }
            
            if($view_stored_image_dims[1] !== null){
                $image->setAttribute("height", $view_stored_image_dims[1]);
            }

            $output[] = Html_TextNode::wrap("span", "Current Image:");
            $output[] = $image;
        }

        if(count($output) == 1){
            return $output[0];
        }
        else{
            return $output;
        }
    }
}
