<?php
namespace Pharven;

class Pharven
{
	/**
	 * Settings defined by user through pharven.json
	 *
	 * @var array
	 */
	protected $settings = [];

	/**
	 * Output name for .phar
	 *
	 * @var string
	 */
	protected $pharName = 'pharven.phar';

	/**
	 * Directories to be included in the .phar
	 *
	 * @var array
	 */
	protected $includeDirs = [];

	/**
	 * Directories to be mounted in the .phar
	 *
	 * @var array
	 */
	protected $mountDirs = [];

	/**
	 * Output directory for .phar
	 *
	 * @var string
	 */
	protected $outputDir;

	/**
	 * @var \Twig_Environment
	 */
	protected $twig;

	public function __construct(array $settings = [])
	{
		// Set name and alias
		$this->setPharName($settings['config']['name'] ?? $this->pharName);

		// Set include directories
		$this->setIncludeDirs($settings['include_dirs'] ?? []);

		// Set mount directories
		$this->setMountDirs($settings['mount_dirs'] ?? []);

		// Set the output directory
		$this->outputDir = isset($argv[0]) ? $argv[0] == '.' ? $_SERVER['PWD'] : $argv[0] : $_SERVER['PWD'];

		// Load Twig
		$loader = new \Twig_Loader_Filesystem(__DIR__ . '/templates');
		$this->twig = new \Twig_Environment($loader);
	}

	/**
	 * Set PHAR name and alias.
	 *
	 * @param string $pharName
	 */
	public function setPharName(string $pharName)
	{
		$this->pharName = $pharName;
	}

	/**
	 * Set directories to include in the PHAR.
	 *
	 * @param array $includeDirs
	 * @throws \Exception
	 */
	public function setIncludeDirs(array $includeDirs)
	{
		if(empty($includeDirs)){
			throw new \Exception('You must define directories to include in phar');
		}

		// Validate include directories
		foreach($includeDirs as $includeDir){
			if(!is_dir($includeDir)){
				throw new \Exception("$includeDir is not a directory");
			}
		}
		$this->includeDirs = $includeDirs;
	}

	/**
	 * Set mount directories
	 * @param array $mountDirs
	 * @throws \Exception
	 */
	public function setMountDirs(array $mountDirs)
	{
		// Validate mount directories
		foreach($mountDirs as $mountDir){
			if(!is_dir($mountDir)){
				throw new \Exception("$mountDir is not a directory!");
			}
		}
		$this->mountDirs = $mountDirs;
	}

	public function makePhar()
	{
		// @todo create phar in /tmp then move to output dir
		$phar = new \Phar($this->pharName, \FilesystemIterator::CURRENT_AS_FILEINFO | \FilesystemIterator::KEY_AS_FILENAME, $this->pharName);

		foreach($this->includeDirs as $includeDir){
			$phar->buildFromDirectory($includeDir);
		}

		// Set the stub
		$phar->setStub($this->twig->render('pharstub.twig', ['mounts' => $this->mountDirs]));
	}
}