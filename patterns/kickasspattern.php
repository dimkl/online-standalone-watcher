<?php
namespace OnlineWacther\Patterns;

/**
 * @see http://kickass.to/tv/?rss=1
 */
class KickAssPattern extends APattern
{
    
    /**
     * [$pattern description]
     * @example Pawn Stars - S12E17 - Last Call Pawn - 720P - HDTV - X265-HEVC - O69
     * @var string
     */
    protected $pattern = '/(.*)s([0-9]{1,2})e([0-9]{1,2})/i';
}