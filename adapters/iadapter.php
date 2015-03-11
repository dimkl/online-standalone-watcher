<?php
interface IAdapter{
    function fetch($incremental=false);
    function &getData($parameter=null);
    function save();
    function specializeData($parameter);
}