<?php namespace SimplePhoto\Storage;

use SimplePhoto\Utils\FileUtils;
use SimplePhoto\Utils\RequestUtils;
use SimplePhoto\Utils\TextUtils;

/**
 * @author Morrison Laju <morrelinko@gmail.com>
 */
class LocalStorage implements StorageInterface
{
    protected $projectRoot;

    protected $savePath;

    /**
     * Constructor
     *
     * @param string $projectRoot Root of your project
     * @param null|string $savePath
     */
    public function __construct($projectRoot, $savePath)
    {
        $this->projectRoot = FileUtils::normalizePath($projectRoot);
        $this->savePath = $savePath;
    }

    /**
     * {@inheritDocs}
     */
    public function upload($file, $destination, array $options = array())
    {
        if (!is_file($file)) {
            throw new \RuntimeException(
                "Unable to upload; File [{$file}] does not exists."
            );
        }

        $fileName = basename($file);
        if ($destination) {
            if (TextUtils::endsWith($destination, "/")) {
                $destination = $destination . $fileName;
            }
        } else {
            $destination = $fileName;
        }

        $savePath = $this->normalizePath($destination, true);
        $this->verifyPathExists(dirname($destination), true);

        if (copy($file, $savePath)) {
            return $this->normalizePath($destination);
        }

        return false;
    }

    /**
     * {@inheritDocs}
     */
    public function getPhotoPath($file)
    {
        return $this->normalizePath($file, true);
    }

    /**
     * {@inheritDocs}
     */
    public function getPhotoUrl($file)
    {
        $path = $this->projectRoot . '/' . ltrim(preg_replace('!^' . $this->projectRoot . '/?!', '', $file), '/');

        return rtrim(str_replace($this->projectRoot, RequestUtils::getBaseUrl(), $path), '/');
    }

    /**
     * {@inheritDocs}
     */
    public function getPhotoResource($file)
    {
        $tmpName = tempnam(sys_get_temp_dir(), 'temp');
        copy($this->normalizePath($file, true), $tmpName);

        return $tmpName;
    }

    /**
     * @return mixed
     */
    public function getSavePath()
    {
        return $this->savePath;
    }

    /**
     * @param $savePath
     */
    public function setSavePath($savePath)
    {
        $this->savePath = $savePath;
    }

    /**
     * @param $directory
     *
     * @return bool
     */
    public function directoryExists($directory)
    {
        return is_dir($this->normalizePath($directory));
    }

    /**
     * @param $directory
     * @param bool $recursive
     * @param int $mode
     *
     * @return bool
     */
    public function createDirectory($directory, $recursive = true, $mode = 0777)
    {
        if ($this->directoryExists($directory)) {
            return true;
        }

        if (mkdir($this->normalizePath($directory), $mode, $recursive)) {
            return true;
        }

        return false;
    }

    /**
     * @param $path
     * @param bool $createIfNotExists
     *
     * @return string
     * @throws \RuntimeException
     */
    public function verifyPathExists($path, $createIfNotExists = false)
    {
        $path = $this->normalizePath($path);

        if (!is_dir($path) && !$createIfNotExists) {
            throw new \RuntimeException(sprintf(
                "Directory: %s not found",
                $path
            ));
        }

        if ($createIfNotExists) {
            $this->createDirectory($path);
        }

        return $path;
    }

    /**
     * @param $path
     * @param bool $withRoot Set to true to prepend project root to the normalized path
     *
     * @return string
     */
    public function normalizePath($path, $withRoot = false)
    {
        if (!FileUtils::isAbsolute($path)) {
            // If the path is not an absolute path,
            // prefix with base
            $path = ($withRoot ? $this->projectRoot . "/" : null) . $this->savePath . "/" . $path;
        }

        return FileUtils::normalizePath($path);
    }
}
