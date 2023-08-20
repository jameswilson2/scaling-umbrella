<?php
require_once 'SystemCommand.class.php';

/**
	@brief ImageMagick image sequence class for IMCommandBuilder
	
	This class represents an ImageMagick image sequence. An image sequence is
	an ordered list of images that are applied to an operation. The image 
	sequence operation creates a new image sequence containing the manipulated
	images.
*/
class IMCommandImageSequence{
	
	private $elements = array();
	private $sequenceOperator = '-flatten';
	private $sequenceOperatorArgs = array();
	
	/**
		@brief Add an image to the image sequence
		@param $filename Image filename
	*/
	public function addImage($filename){
		assert(file_exists($filename));
		$this->elements[] = $filename;
	}
	
	/**
		@brief Add option setting
		@param $name setting name
		@param $value setting value
	*/
	public function addSetting($name, $value){
		
		if(!is_array($value)){
			$value = array($value);
		}
		
		$this->elements[] = "-$name";
		$this->elements = array_merge($this->elements, $value);
	}
	
	/**
		@brief Alias for addSetting method
	*/
	public function addImageOperation($name, $value){
		$this->addSetting($name, $value);
	}
	
	/**
		@brief Set the image sequence operation
		
		See http://www.imagemagick.org/script/command-line-options.php for a
		list of operations.
	*/
	public function setSequenceOperation($operator, $value = array()){
		$this->sequenceOperator = "-$operator";
		
		if(!is_array($value) ){
			$value = array($value);
		}
		
		$this->sequenceOperatorArgs = $value;
	}
	
	public function createCommandArguments(){
		return array_merge($this->elements, array($this->sequenceOperator), $this->sequenceOperatorArgs);
	}
}

/**
	@brief ImageMagick command builder class
*/
class IMCommandBuilder{
	
	private $command = '';
	private $imageSequences = array();
	private $outputImage = '';
	
	/**
		@param $command Optional argument. Set the name of the ImageMagick 
		program to be used.
	*/
	public function __construct($command = 'convert'){
		$this->command = $command;
	}
	
	/**
		@brief Create a new image sequence
		
		This function creates a new command sequence object, adds it to the image
		sequence array in this command builder object and then returns it to the 
		caller.
		
		@see IMCommandSequence
		@return IMCommandSequence object
	*/
	public function createImageSequence(){
		$imageSequence = new IMCommandImageSequence();
		$this->imageSequences[] = $imageSequence;
		return $imageSequence;
	}
	
	/**
		@brief Set the filename for the output image
	*/
	public function setOutputImage($filename){
		$this->outputImage = $filename;
	}
	
	/**
		@brief Create the command
		Build the command and return it as a SystemCommand object.
		@return SystemCommand object
	*/
	public function createCommand(){
		
		$args = array();
		
		foreach($this->imageSequences as $sequence){
			$args = array_merge($args, $sequence->createCommandArguments());
		}
		
		$args[] = $this->outputImage;
		
		return new SystemCommand($this->command, $args);
	}
}
