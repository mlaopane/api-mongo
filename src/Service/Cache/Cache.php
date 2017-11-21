<?php
namespace MykeOn\Service\Cache;

use Psr\SimpleCache\CacheInterface;
use MykeOn\Helper\String\StringObject;

class Cache implements CacheInterface
{
    /**
     * @var string
     */
    private $baseDirectoryPath;

    /**
     * @var string
     */
    private $directoryPath;

    public function __construct()
    {
        $this->baseDirectoryPath = dirname(dirname(dirname(__DIR__)))."/cache";
        $this->directoryPath = $this->baseDirectoryPath;
    }

    /**
     * @param  $key
     * @param  null  $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if ($this->has((string) $key)) {
            $file = fopen("$this->directoryPath/$key", 'r');
            $result = json_decode(fgets($file));
            fclose($file);
            return $result;
        }
        return $default;
    }

    /**
     * @param          $key
     * @param mixed    $value
     * @param int|null $ttl
     */
    public function set($key, $value, $ttl = null)
    {
        $filename = "$this->directoryPath/$key";
        $file = fopen($filename, 'w');
        fwrite($file, $value);
        fclose($file);

        return $this;
    }

    public function delete($key)
    {
        if ($this->has($key)) {
            unlink("$this->directoryPath/$key");
        }
    }

    public function clear()
    {
        $dirIterator = new \DirectoryIterator($this->directoryPath);

        foreach ($dirIterator as $file) {
            if (!$file->isDot()) {
                unlink($file->getPathname());
            }
        }

        return count($dirIterator) === 0;
    }

    /**
     * [getKeys description]
     * @param  string $prefix [description]
     * @return [type]         [description]
     */
    public function getKeys($prefix = '')
    {
        $dirIterator = new \DirectoryIterator($this->directoryPath);
        $keys = [];

        foreach ($dirIterator as $key => $file) {
            $filename = new StringObject($file->getFilename());
            if (!$file->isDot() && $filename->startWith((string) $prefix)) {
                $keys[] = $filename->getString();
            }
        }

        return $keys;
    }

    public function getMultiple($keys, $default = null)
    {

    }

    public function setMultiple($values, $ttl = null)
    {

    }

    public function deleteMultiple($keys)
    {
        $dirIterator = new \DirectoryIterator($this->directoryPath);

        foreach ($dirIterator as $file) {
            if (!$file->isDot() && in_array($file->getFilename(), $keys)) {
                unlink($file->getPathname());
            }
        }
    }

    public function has($key)
    {
        $filename = "$this->directoryPath/$key";
        if ($file = @fopen($filename, 'r')) {
            fclose($file);
            return true;
        }
        return false;
    }

    public function setSubDirectory(string $subDirectory)
    {
        $this->directoryPath = "$this->baseDirectoryPath/$subDirectory";
        if (!is_dir($this->directoryPath)) {
            mkdir($this->directoryPath, 0777, true);
        }

        return $this;
    }
}
