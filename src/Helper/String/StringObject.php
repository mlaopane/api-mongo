<?php
namespace MykeOn\Helper\String;

class StringObject
{
    /**
     * @var string
     */
    private $string;
    
    public function __construct(string $string = '')
    {
        $this->string = $string;
    }
    
    /**
     * @param  string $needle
     *
     * @return bool
     */
    public function startWith(string $needle): bool
    {
        $length = strlen($needle);
        return (substr($this->string, 0, $length) === $needle);
    }

    /**
     * @param  string $needle
     *
     * @return bool
     */
    function endsWith(string $needle): bool
    {
        $length = strlen($needle);
        return $length === 0 || (substr($this->string, -$length) === $needle);
    }

    /**
     * Get the value of String 
     * 
     * @return string
     */
    public function getString()
    {
        return $this->string;
    }
 
    /** 
     * Set the value of String 
     * 
     * @param string string
     * 
     * @return self
     */
    public function setString($string)
    {
        $this->string = $string;
 
        return $this;
    }
 
}
