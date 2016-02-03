<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * CParser
 *
 * A Codeigniter library that make crawl with just few lines of code.
 *
 * Copyright (C) 2011 through 2015  Dima Korobka.
 *
 * LICENSE
 *
 * CParser is released with dual licensing, using the GPL v3 (license-gpl3.txt) and the MIT license (license-mit.txt).
 * You don't have to do anything special to choose one license or the other and you don't have to notify anyone which license you are using.
 * Please see the corresponding license file for details of these licenses.
 * You are free to use, modify and distribute this software, but all copyright information must remain.
 *
 * @package    	image CRUD
 * @copyright  	Copyright (c) 2011 through 2012, John Skoumbourdis
 * @license    	https://github.com/korobkadima/cparser
 * @version    	0.5
 * @author     	John Skoumbourdis <scoumbourdisj@gmail.com>
 */
 
class CParser
{
	private   $in = array();
	private   $html = '';
	
	protected $out = array();
	protected $pattern = array(); 
	protected $out_encoding = ''; 
	protected $in_encoding  = ''; 
	protected $url = '';
	protected $tags = '';
	
//	private $html;
	
	function __construct() 
	{
		log_message('debug', 'CParser library initialized.');
	}
		
	public function set_url($url)
	{
		$this->url = $url;
		
		return $this->url;
	}
	
	public function set_out_encoding($out_encoding)
	{
		$this->out_encoding = $out_encoding;
	}
	
	public function set_in_encoding($in_encoding)
	{
		$this->in_encoding = $in_encoding;
	}
	
	public function set_allow_tags($tags)
	{
		$this->tags = $tags;
	}
	
	public function set_pattern($pattern)
	{
		if(!is_array($pattern))
		{
			throw new Exception('Ïàòòåğíû ïåğåäàíû íå ìàññèâîì èëè íå âåğíî');
		}
		
		foreach($pattern as $key => $val)
		{
			$this->pattern[$key] = $val;
		}
	}
	
	private function get_html_by_url($url)
	{
		$this->html = file_get_contents($url);
	
		if ( ! $this->html)
		{
			throw new Exception('Ñàéò íå îòäàë èíôîğìàöèş... èëè Íå ğàáîòàåò file_get_contents');
		}
		
		return $this->html;
	}
	
	public function get_one()
	{
		$this->html = $this->get_html_by_url($this->url);
	
		$this->html = iconv($this->out_encoding, $this->in_encoding.'//TRANSLIT', $this->html); 
			
		if(!is_array($this->pattern))
		{
			throw new Exception('Ïàòòåğíû ïåğåäàíû íå ìàññèâîì èëè íå âåğíî'); 
		} 
		
		if(count($this->pattern) == 0)
		{
			throw new Exception('Ïàòòåğíû íå óñòàíîâëåíû'); 
		} 
		
		foreach($this->pattern as $key => $val)
		{
			preg_match('#'.$val.'#sUi',$this->html,$this->in[$key]); 
		}
		
		foreach($this->in as $key => $val)
		{
			if(isset($val[1]))
			{
				#÷èñòèì ëèøíèå òåãå
				if(isset($this->tags))
				{
					$val[1] = strip_tags($val[1],$this->tags);
				}
				
				# ÷èñòèì ëèøíèå ïğîáåëû
				$val[1] = preg_replace('#\n\t\s#',"",$val[1]);
				$val[1] = preg_replace('/\s{2,}/'," ",$val[1]);
			
				$this->out[$key] = trim($val[1]);
			}
		}
		
		return $this->out; 
	}
	
	public function get_all()
	{
		$this->html = $this->get_html_by_url($this->url);
	
		$this->html = iconv($this->out_encoding, $this->in_encoding.'//TRANSLIT', $this->html); 
			
		if(!is_array($this->pattern))
		{
			throw new Exception('Ïàòòåğíû ïåğåäàíû íå ìàññèâîì èëè íå âåğíî'); 
		} 
		
		if(count($this->pattern) == 0)
		{
			throw new Exception('Ïàòòåğíû íå óñòàíîâëåíû'); 
		} 
		
		foreach($this->pattern as $key => $val)
		{
			preg_match_all('#'.$val.'#sUi',$this->html,$this->in[$key]); 
		}
		
		foreach($this->in as $key => $val)
		{
			if(isset($val[1]))
			{
				foreach($val[1] as $one)
				{
					#÷èñòèì ëèøíèå òåãå
					if($this->tags)
					{
						$one = strip_tags($one,$this->tags);
					}
					
					# ÷èñòèì ëèøíèå ïğîáåëû
					$one = preg_replace('#\n\t\s#',"",$one);
					$one = preg_replace('/\s{2,}/'," ",$one);
				
					$this->out[$key][] = trim($one); 
				}
			}
		}
		
		return $this->out; 
	}
	
}