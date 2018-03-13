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
     * Output name for .phar
     *
     * @var string
     */
    protected $pharName = 'pharven.phar';

    /**
     * Vendor directory
     *
     * @var string
     */
    protected $vendorDir;

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
     * @var Twig_Environment
     */
    protected $twig;

    /**
     * Pharven constructor.
     *
     * @param string $vendorDir
     * @param array $userSettings
     * @throws \Exception
     */
    public function __construct(string $vendorDir, array $userSettings = [])
    {
        // Set vendor directory
        $this->setVendorDir($vendorDir);

        // Set include directories
        $this->setIncludeDirs($userSettings['include_dirs'] ?? []);

        // Set mount directories
        $this->setMountDirs($userSettings['mount_dirs'] ?? []);

        // Load Twig
        $loader = new Twig_Loader_Filesystem(__DIR__ . '/templates');
        $this->twig = new Twig_Environment($loader);
    }

    /**
     * Set path to vendor directory.
     *
     * @param string $vendorDir
     * @throws \Exception
     */
    public function setVendorDir($vendorDir)
    {
        if (!is_dir($vendorDir)) {
            throw new \Exception("$vendorDir is not a directory!");
        }
        $this->vendorDir = $vendorDir;
    }

    /**
     * Set directories to include in the PHAR.
     *
     * @param array $includeDirs
     * @throws \Exception
     */
    public function setIncludeDirs(array $includeDirs)
    {
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
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function makePhar(): bool
    {
        // Create phar
        $phar = new \Phar($this->pharName, \FilesystemIterator::CURRENT_AS_FILEINFO | \FilesystemIterator::KEY_AS_FILENAME, $this->pharName);

        // Include vendor directory
        $phar->buildFromDirectory($this->vendorDir);

        // Include user defined directories
        foreach ($this->includeDirs as $includeDir) {
            $phar->buildFromDirectory($includeDir);
        }

        // Set the stub
        return $phar->setStub($this->twig->render('pharstub.twig', ['mounts' => $this->mountDirs]));
    }
}
