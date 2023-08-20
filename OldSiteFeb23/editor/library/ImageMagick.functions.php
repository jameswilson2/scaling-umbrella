<?php
require_once 'IMCommandBuilder.class.php';

class ImageMagick{

	/**
		
	*/
	static function scaleCropFit($source, $destination, $width, $height, $gravity = "center"){
	
		assert(is_readable($source));
		
		$existing_size = getimagesize($source);
		$source_width = $existing_size[0];
		$source_height = $existing_size[1];
	
		// Output 638 x 398   (w/h 1.6) (h/w 0.6)
		// Output 57x42 (1.35) (0.7)
		// Input 2705x2020 (1.33) (0.7)
		// Input 109x26 (4.19) (0.23)
		
		if($source_width/$source_height >= $width/$height){
			$size = "x{$height}";
		}
		else{
			$size = "{$width}x";
		}
		
		$command = new IMCommandBuilder;
		
		$sequence = $command->createImageSequence();
		$sequence->addImage($source);
		$sequence->addSetting("quality", 75);
		$sequence->setSequenceOperation("resize", $size);
	
		$sequence = $command->createImageSequence();
		$sequence->addSetting("gravity", $gravity);
		$sequence->setSequenceOperation("extent", "{$width}x{$height}");
		
		$command->setOutputImage($destination);
		
		$command = $command->createCommand();
		$command->execute();
		$command->close();
	}

	/**
		Scale down an image to fit a given size. If the source image is already
		smaller enough then no resizing of the source image occurs. The output
		image has its width and height padded with white space to make sure the
		size matches the given width and height arguments.
		
		This function is useful for making thumbnails.
		
		@param $source Filename of the source image
		@param $destination Filename for the output image
		@param $width Set the width size of the output image
		@param $height Set the height size of the output image
		@param $background Optional argument. Set the background colour of the
			   padded area.
		@param $gravity Optional argument. Set which sides of the image to add
		       padding from. For example, center gravity adds padding to all
			   sides of the image resulting in the parallel sides having equal
			   padding.
	*/
	static function shrink($source, $destination, $width, $height, $background = "white", $gravity = "center"){
		
		assert(is_readable($source));
		
		$existing_size = getimagesize($source);
		$size = "{$width}x{$height}";
		
		$command = new IMCommandBuilder;
		
		if($existing_size[0] > $width || $existing_size[1] > $height){
			$sequence = $command->createImageSequence();
			$sequence->addImage($source);
			$sequence->setSequenceOperation("resize", $size);
			$shrinked = true;
		}
		
		$sequence = $command->createImageSequence();
		if(!$shrinked) $sequence->addImage($source);
		$sequence->addSetting("background", $background);
		$sequence->addSetting("gravity", $gravity);
		$sequence->setSequenceOperation("extent", $size);
		
		$command->setOutputImage($destination);
		
		$command = $command->createCommand();
		$command->execute();
		$command->close();
	}
	
	/**
		@deprecated Use the shrink function instead.
	*/
	static function resizeAndExtent($source, $destination, $width, $height, $background = "white", $gravity = "center"){
		
		assert(is_readable($source));
		
		$command = new IMCommandBuilder;
		
		$size = "{$width}x{$height}";
		
		$sequence = $command->createImageSequence();
		$sequence->addImage($source);
		$sequence->setSequenceOperation("resize", $size);
		
		$sequence = $command->createImageSequence();
		$sequence->addSetting("background", $background);
		$sequence->addSetting("gravity", $gravity);
		$sequence->setSequenceOperation("extent", $size);
		
		$command->setOutputImage($destination);
		
		$command = $command->createCommand();
		$command->execute();
		$command->close();
	}
}
