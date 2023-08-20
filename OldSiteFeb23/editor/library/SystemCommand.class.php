<?php

/**
	
*/
class SystemCommand{

	const FILE_IO_TIMEOUT = 10;
	
	private $command = array();
	private $redirects = array();
	private $env = null;
	private $cwd = null;
	private $process = null;
	private $exitCode = -1;
	
	public function __construct($program, $arguments = array()){
	
		$this->redirects = array(
			array('pipe', 'r'),
			array('pipe', 'w'),
			array('pipe', 'w')
		);
		
		assert($program != '');
		assert(is_array($arguments));
		
		$this->command = escapeshellcmd($program);
		
		foreach($arguments as $argument){
			$this->command .= ' ' . escapeshellarg($argument);
		}
	}
	
	private function addArgument($argument){
		$this->arguments[] = escapeshellarg($argument);
	}
	
	public function setEnvironmentVariables($env){
		assert(is_array($env) || is_null($env));
		$this->env = $env;
	}
	
	public function setWorkingDirectory($cwd){
		assert(is_string($cwd) || is_null($cwd));
		$this->cwd = $cwd;
	}
	
	private function setRedirect($descriptor, $stream){
		assert(is_resource($stream));
		$this->redirects[$descriptor] = $stream;
	}
	
	public function setStdInput($readableStream){
		assert(is_null($this->process));
		$this->setRedirect(0, $readableStream);
	}
	
	public function setStdOutput($writableStream){
		assert(is_null($this->process));
		$this->setRedirect(1, $writableStream);
	}
	
	public function setStdError($writableStream){
		assert(is_null($this->process));
		$this->setRedirect(2, $writableStream);
	}
	
	public function execute(){
		
		$this->process = @proc_open($this->command, $this->redirects, $pipes, $this->cwd, $this->env);
		
		if($this->process === false){
			return false;
		}

		$this->redirects = $pipes;
		
		foreach($this->redirects as $file){
			stream_set_timeout($file, self::FILE_IO_TIMEOUT);
		}
		
		return true;
	}
	
	public function debug(){
		return $this->command;
	}
	
	public function getStdOutput(){
		assert(is_resource($this->process));
		return $this->redirects[1];
	}
	
	public function getStdError(){
		assert(is_resource($this->process));
		return $this->redirects[2];
	}
	
	public function close(){
		
		$status = proc_get_status($this->process);
		
		foreach($this->redirects as $file){
			fclose($file);
		}
		
		$exitCode = proc_close($this->process);
		
		$this->process = null;
		
		$this->exitCode = ($status['running'] ? $exitCode : $status['exitcode']);
		return $this->exitCode;
	}
	
	public function getExitCode(){
		return $this->exitCode;
	}
}
