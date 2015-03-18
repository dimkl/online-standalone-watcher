<?php 
namespace OnlineWacther\Patterns;

/**
 * @see http://showrss.info/feeds/all.rss
 */
class ShowRssPattern extends APattern
{
    
    /**
     * [$pattern description]
     * @example Pawn Stars 1x04 Knights in Fake Armor? 720p
     * @var string
     */
    protected $pattern = '/(.*)([0-9]{1,2})x([0-9]{1,2})/i';
}