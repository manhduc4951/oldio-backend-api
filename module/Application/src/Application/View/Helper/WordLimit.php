<?php
namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;

class WordLimit extends AbstractHelper
{  
    /**
     * Word Limiter
     *
     * Limits a string to X number of words.
     *
     * @access	public
     * @param	string
     * @param	integer
     * @param	string	the end character. Usually an ellipsis
     * @return	string
     */
    public static function __invoke($str, $limit = 100, $end_char = '&#8230;')
	{  
		if (trim($str) == '')
		{
			return $str;
		}

		preg_match('/^\s*+(?:\S++\s*+){1,'.(int) $limit.'}/', $str, $matches);

		if (strlen($str) == strlen($matches[0]))
		{
			$end_char = '';
		}

		return rtrim($matches[0]).$end_char;
	}
}