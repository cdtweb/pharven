<?php declare(strict_types=1);
namespace Pharven;

use Twig_Environment;
use Twig_Loader_Filesystem;

/**
 * Class Pharven
 * @package Pharven
 */
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
     * @var Twig_Environment
     */
    protected $twig;

    /**
     * Pharven constructor.
     *
     * @param array $settings
     */
    public function __construct(array $settings = [])
    {
        // Set include directories
        $this->setIncludeDirs($settings['include_dirs'] ?? []);

        // Set mount directories
        $this->setMountDirs($settings['mount_dirs'] ?? []);

        // Load Twig
        $loader = new Twig_Loader_Filesystem(__DIR__ . '/templates');
        $this->twig = new Twig_Environment($loader);
    }

    /**
     * Set directories to include in the PHAR.
     *
     * @param array $includeDirs
     * @throws \Exception
     */
    public function setIncludeDirs(array $includeDirs)
    {
        if (empty($includeDirs)) {
            throw new \Exception('Include directories must be configured in pharven.json');
        }

        // Validate include directories
        foreach ($includeDirs as $includeDir) {
            if (!is_dir($includeDir)) {
                throw new \Exception("$includeDir is not a directory!");
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
        foreach ($mountDirs as $mountDir) {
            if (!is_dir($mountDir)) {
                throw new \Exception("$mountDir is not a directory!");
            }
        }
        $this->mountDirs = $mountDirs;
    }

    /**
     * @return array
     */
    public function getIncludeDirs(): array
    {
        return $this->includeDirs;
    }

    /**
     * @return array
     */
    public function getMountDirs(): array
    {
        return $this->mountDirs;
    }

    /**
     * Create the PHAR
     *
     * @return boolean
     */
    public function makePhar(): bool
    {
        $phar = new \Phar($this->pharName, \FilesystemIterator::CURRENT_AS_FILEINFO | \FilesystemIterator::KEY_AS_FILENAME, $this->pharName);

        foreach ($this->includeDirs as $includeDir) {
            $phar->buildFromDirectory($includeDir);
        }

        // Set the stub
        return $phar->setStub($this->twig->render('pharstub.twig', ['mounts' => $this->mountDirs]));
    }
}
