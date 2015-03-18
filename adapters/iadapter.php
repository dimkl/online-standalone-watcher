<?php
namespace OnlineWacther\Adapters;
/**
 * Iadapter Interface
 */
interface IAdapter {
    
    /**
     * [fetch description]
     * @param  boolean $incremental [description]
     */
    function fetch($incremental = false);
    
    /**
     * [getData description]
     * @param  string $parameter [description]
     * @return array
     */
    function &getData($parameter = null);
    
    /**
     * [save description]
     * @return [type] [description]
     */
    function save();
    
    /**
     * [specializeData description]
     * @param  [type] $parameter [description]
     * @return [type]            [description]
     */
    function specializeData($parameter);
}
