<?php
namespace OnlineWacther\Patterns;
/**
 * APattern abstract class
 */
abstract class APattern
{
    
    /**
     * [$pattern description]
     * @var [type]
     */
    protected $pattern;
    
    /**
     * [match description]
     * @param  [type] $value    [description]
     * @param  [type] &$matches [description]
     * @return [type]           [description]
     */
    public function match($value, &$matches) {
        $result = preg_match($this->pattern, $value, $matches);
        
        // remove full match from array
        array_shift($matches);
        return $result;
    }
}

