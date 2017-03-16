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
	 * Alias for phar:// stream
	 *
	 * @var string
	 */
	protected $pharAlias = 'pharven.phar';

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

	protected $outputDir;
	protected $twig;

	public function __construct(array $settings = [])
	{
		// Set name and alias
		$this->setPharName(
			$settings['config']['phar_name'] ?? $this->pharName,
			$settings['config']['phar_alias'] ?? $this->pharAlias
		);

		// Set include directories
		$this->setIncludeDirs($settings['include_dirs'] ?? []);

		// Set mount directories
		$this->setMountDirs($settings['mount_dirs'] ?? []);

		global $argv;
		array_shift($argv);

		$this->outputDir = isset($argv[0]) ? $argv[0] == '.' ? $_SERVER['PWD'] : $argv[0] : $_SERVER['PWD'];

		// Load Twig
		$loader = new \Twig_Loader_Filesystem(__DIR__ . '/templates');
		$this->twig = new \Twig_Environment($loader);
	}

	/**
	 * Set PHAR name and alias.
	 *
	 * @param string $pharName
	 * @param null $pharAlias
	 */
	public function setPharName(string $pharName, $pharAlias = null)
	{
		$this->pharName = $pharName;
		$this->pharAlias = $pharAlias ?? $pharName;
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

		// @todo Validate the directories exist
		$this->includeDirs = $includeDirs;
	}

	/**
	 * Set mount directories
	 * @param array $mountDirs
	 */
	public function setMountDirs(array $mountDirs)
	{
		// @todo Validate that directories exist
		$this->mountDirs = $mountDirs;
	}

	public function makePhar()
	{
		// @todo create phar in /tmp then move to output dir
		$phar = new \Phar($this->pharName, \FilesystemIterator::CURRENT_AS_FILEINFO | \FilesystemIterator::KEY_AS_FILENAME, $this->pharAlias);

		foreach($this->includeDirs as $includeDir){
			$phar->buildFromDirectory($includeDir);
		}

		// Set the stub
		$phar->setStub($this->twig->render('pharstub.twig', ['mounts' => $this->mountDirs]));
	}
}