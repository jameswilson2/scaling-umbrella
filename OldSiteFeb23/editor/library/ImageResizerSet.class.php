<?php
require_once "FilesystemUtils.php";
require_once "ImageResizer.class.php";

class ImageResizerSet{
    
    private $resizers = array();
    
    public function __construct(){
        
    }

    public function addResizer($destination_dir, $resizer){
        
        if(!is_dir($destination_dir) || !is_writable($destination_dir)){
            throw new Exception("Directory '$destination_dir' not found or has no write permission");    
        }
        
        if(is_array($resizer)){
            
			$settings = $resizer;
			
			$resizer = new ImageResizer;
            $resizer->setOutputSize($settings["width"], $settings["height"]);
			
			foreach($settings as $setting_name => $setting_value){
				switch($setting_name){
					case "fixed_size":
						$resizer->setOutputFixedSize($setting_value);
						break;
					case "crop":
						$resizer->setToCrop($setting_value);
						break;
					case "grow":
						$resizer->setToGrow($setting_value);
						break;
					case "shrink":
						$resizer->setToShrink($setting_value);
						break;
					case "quality":
						$resizer->setOutputQuality($setting_value);
						break;
					case "scale":
						$resizer->setToScale($setting_value);
						break;
				}
			}
        }
        
        $this->resizers[] = array(
            "dir" => $destination_dir,
            "resizer" => $resizer
        );
    }
    
    public function resize($sample_image_filename, $output_basename = null){
        
        if($output_basename === null){
            $sample_image_info = pathinfo($sample_image_filename);
            $output_basename = $sample_image_info["basename"];
        }
        
        foreach($this->resizers as $resizer_conf){
            
            $dir = $resizer_conf["dir"];
            $destination = $dir . "/$output_basename";
            
            $resizer = $resizer_conf["resizer"];
            $resizer->resize($sample_image_filename, $destination);
        }
    }
    
    public function getOutputDirectories(){
        $dirs = array();
        foreach($this->resizers as $resizer){
            $dirs[] = $resizer["dir"];
        }
        return $dirs;
    }
}

function upload_image($upload, $resizers){
    
    if(is_string($upload)){
        $upload_name = $upload;
        $upload = new FileUpload($upload_name, "image");
        if(!$upload->isValidUpload()){
            return false;
        }
    }
    
    $output_dirs = $resizers->getOutputDirectories();
    $basename = unique_basename($upload->getName(), $output_dirs);
    $resizers->resize($upload->getTempFilename(), $basename);
	
	return $basename;
}

