<?php
class cURL 
{

	private $headers;
	private $user_agent;
	private $compression;
	private $cookieFile;	
    private $verbose;
	
	public function __construct($cookie='./cookies.txt',$compression='gzip',$verbose=false) 
	{
		$this->headers[] = 'Accept: image/gif, image/x-bitmap, image/jpeg, image/pjpeg';
		$this->headers[] = 'Connection: Keep-Alive';
		$this->user_agent = 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.0.3705; .NET CLR 1.1.4322; Media Center PC 4.0)';
		$this->compression=$compression;
        $this->cookieFile=$cookie;
        
        // create cookie file first
		$this->checkCookiePermission();
        
        $this->verbose = $verbose;
	}
	
	private function checkCookiePermission() 
	{
		// issue an error if we do not have permission to save our cookie
        $f = fopen($this->cookieFile,'w');
        
        if(!$f) 
            die('The cookie file could not be opened. Make sure this directory has the correct permissions');
        else 
            fclose($f);
	}
    
    private function init($url) 
    {
        $process = curl_init($url);
		curl_setopt($process, CURLOPT_HTTPHEADER, $this->headers);
		curl_setopt($process, CURLOPT_HEADER, 0);
		curl_setopt($process, CURLOPT_USERAGENT, $this->user_agent);
        curl_setopt($process, CURLOPT_AUTOREFERER, true);

        // do we need cookies ?
		if ($this->cookieFile != '')
        {
            curl_setopt($process, CURLOPT_COOKIEFILE, $this->cookieFile);
            curl_setopt($process, CURLOPT_COOKIEJAR, $this->cookieFile);
        }            
		
		curl_setopt($process,CURLOPT_VERBOSE , $this->verbose?1:0);
        curl_setopt($process,CURLOPT_ENCODING , $this->compression);
		curl_setopt($process, CURLOPT_TIMEOUT, 30);
        curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($process, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
        
        return $process;
    }
	
	public function get($url) 
	{
		// init curl
        $process = $this->init($url);
        
        // exec
		$return = curl_exec($process);
        
        // close
		curl_close($process);
		
        // return data
		return $return;
	}
	public function post($url,$data) 
	{
		// init curl
        $process = $this->init($url);
		
        // convert post data to var=encodedvar&var1=encodedvar1
        $post = '';
        foreach($data as $k=>$v) {
                if($post=='')
                    $post= $k.'='.urlencode($v);
                else
                    $post.='&'.$k.'='.urlencode($v);
        }
        
        // set post fields, and post opt
		curl_setopt($process, CURLOPT_POSTFIELDS, $post);
		curl_setopt($process, CURLOPT_POST, 1);
		
        // exec
		$return = curl_exec($process);
        
        // close
		curl_close($process);
		
        // return
		return $return;
	}
}

?>