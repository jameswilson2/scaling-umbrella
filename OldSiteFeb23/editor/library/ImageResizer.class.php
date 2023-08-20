<?php
require_once 'IMCommandBuilder.class.php';

class ImageResizer
{
	private $gravity = "center";
	private $background = "white";
	private $scale = true;
	private $grow = false;
	private $shrink = true;
	private $crop = true;
	private $output_fixed_size = true;
	private $output_quality = 75;
	private $output_width = null;
	private $output_height = null;
	
	public function setToScale($value){
		$this->scale = $value;
	}
	
	public function setToCrop($value){
		$this->crop = $value;
	}
	
	public function setToGrow($value){
		$this->grow = $value;
	}

	public function setToShrink($value){
		$this->shrink = $value;
	}

	public function setOutputQuality($quality){
		assert($quality >= 0 && $quality <= 100);
		$this->output_quality = $quality;
	}
	
	public function setOutputFixedSize($value){
		$this->output_fixed_size = $value;
	}
	
	public function setOutputSize($width, $height){
		$this->output_width = $width;
		$this->output_height = $height;
	}
	
	public function resize($source, $destination){
		
		assert($this->output_width && $this->output_height);
		assert(is_readable($source));
		
		$output_aspect_ratio = $this->output_width/$this->output_height;
		
		$existing_size = getimagesize($source);
		$source_width = $existing_size[0];
		$source_height = $existing_size[1];
		$source_aspect_ratio = $source_width/$source_height;
		$source_smaller = $source_width < $this->output_width && $source_height < $this->output_height;
		
		$command = new IMCommandBuilder;
		$added_source_image = false;
		
		$allow_resize = $this->grow && $source_smaller || $this->shrink && !$source_smaller;
		if($allow_resize){
		
			if($this->scale){
				if($this->crop){
					// The source image width is longer or will grow to be longer
					if($source_aspect_ratio > $output_aspect_ratio){
						// Resize by height
						$size = "x{$this->output_height}";
					}
					else{
						// Resize by width
						$size = "{$this->output_width}x";
					}
				}
				else{
					$size = "{$this->output_width}x{$this->output_height}";
				}
			}
			else{
				$size = "{$this->output_width}x{$this->output_height}!";
			}
			
			$sequence = $command->createImageSequence();
			$sequence->addImage($source);
			$sequence->addSetting("quality", $this->output_quality);
			$sequence->setSequenceOperation("resize", $size);
			
			$added_source_image = true;
		}
		
		if($this->output_fixed_size || $this->crop){
			$sequence = $command->createImageSequence();
			if(!$added_source_image){
				$sequence->addImage($source);
				$added_source_image = true;
			}
			$sequence->addSetting("quality", $this->output_quality);
			$sequence->addSetting("gravity", $this->gravity);
			$sequence->addSetting("background", $this->background);
			$sequence->setSequenceOperation("extent", "{$this->output_width}x{$this->output_height}");
		}
		
		if(!$added_source_image){
			$sequence = $command->createImageSequence();
			$sequence->addImage($source);
			$added_source_image = true;
		}
		
		$command->setOutputImage($destination);
		
		$command = $command->createCommand();
		$command->execute();
		
		/*$error_output = stream_get_contents($command->getStdError());
		if($error_output){
			trigger_error($error_output, E_USER_WARNING);
		}*/
		
		$command->close();
	}
};
