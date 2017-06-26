<?php
class Path
{
	public $currentPath;

	function __construct($path)
	{
		$this->currentPath = $path;
	}

	public function cd($newPath)
	{
		$n = explode("/",$this->currentPath);
		$c = explode("../",$newPath);

		
		$this->currentPath = "/a/b/c/x";
	}
}

$path = new Path('/a/b/c/d');
$path->cd('../x');
echo $path->currentPath;


