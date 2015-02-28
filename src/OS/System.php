<?php
namespace Peridot\WebDriverManager\OS;

/**
 * System determines information about the operating system.
 *
 * @package Peridot\WebDriverManager\OS
 */
class System implements SystemInterface
{
    /**
     * Darwin pattern
     *
     * @var string
     */
    private static $darwin = '/^dar/i';

    /**
     * Windows
     *
     * @var string
     */
    private static $windows = '/^win/i';

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function isMac()
    {
        return preg_match(self::$darwin, PHP_OS);
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function isWindows()
    {
        return preg_match(self::$windows, PHP_OS);
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function isLinux()
    {
        $notMac = ! $this->isMac();
        $notWindows = ! $this->isWindows();

        return $notMac && $notWindows;
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function is64Bit()
    {
        return PHP_INT_SIZE === 8;
    }
} 
