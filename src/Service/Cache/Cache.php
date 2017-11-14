<?php
namespace MykeOn\Service\Cache;

use Psr\SimpleCache\CacheInterface;

class Cache implements CacheInterface
{
    /**
     * @var string
     */
    private $cacheDir;

    public function __construct()
    {
        $this->cacheDir = dirname(dirname(dirname(__DIR__)))."/cache";
    }

    /**
     * @param  $key
     * @param  null  $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if ($this->has($key)) {
            $file = fopen($this->cacheDir.$key, 'r');
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
        $filename = $this->cacheDir."/$key";
        $file = fopen($filename, 'w');
        fwrite($file, $value);
        fclose($file);

        return $this;
    }

    public function delete($key)
    {

    }

    public function clear()
    {

    }

    public function getMultiple($keys, $default = null)
    {

    }

    public function setMultiple($values, $ttl = null)
    {

    }

    public function deleteMultiple($keys)
    {

    }

    public function has($key)
    {
        $filename = $this->cacheDir.$key;
        if ($file = @fopen($filename, 'r')) {
            fclose($file);
            return true;
        }
        return false;
    }

    public function addSubDir(string $subDir)
    {
        $this->cacheDir .= "/$subDir";
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
        return $this;
    }
}
