<?php
/*
 * Copyright 2010 - Robson Dantas <biu.dantas@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

require_once 'utils/error.php';
class Scrap {

	private $uid;
	private $message;
	private $orkutApi;
	
	public function __construct($orkut) {
			$this->orkutApi = $orkut;
	}
	
    public function fetchScraps() {

		$msg = array('method' => 'messages.get',
			     'params' => array('userId' => array('@me'),'groupId' => '@friends', 'pageType' =>'first', 'messageType'=>'public_message'));
		
		// add current user to the batch
		$this->orkutApi->addRequest($msg,'scraps');
        
        $exec = $this->orkutApi->execute();

		if(isset($exec['scraps']['error']))
			GenericError::stop(1,$result['scraps']['error']['message']);
		
		$result[] = array('id'=>'0','message'=>'ok');
		$result[] = $exec;
        
        return $result;
		
	}

	public function send($uids, $msg) {
	
		if(count($uids)==0)
			GenericError::stop(1,'No uids specified!');
	
		// batch messages
        $i=0;
		foreach($uids as $uid) {
			$message = array('method' => 'messages.create',
			 'params' => array('userId' => array($uid), 
					   'groupId' => '@self', 
					   'message' => array('recipients' => array(1), 
								  'body' => $msg, 
								  'title' => 'sent at '. strftime('%X')), 
								  'messageType'=> 'public_message'));
					   
			$this->orkutApi->addRequest($message, $i++."_".$uid);
		}
		
		$ret = Array();

		$exec = $this->orkutApi->execute();

		$ret[] = $this->checkError($exec);
//print_r($exec);die;
		// execute and return a json		
	//	return json_encode($exec);
		return $ret;
		
	}
    
    public function setCaptchaRequest($captchaToken, $captchaValue) {

		$captcha = array('method' => 'captcha.answer',
						 'params' => array('userId' => array('@me'),
								   'groupId' => '@self',
								   'captchaAnswer' => $captchaValue,
								   'captchaToken' => $captchaToken));
				//print_r($captcha);die;
		$this->orkutApi->addRequest($captcha,'captcha');
	}

	// basically captcha and generic error handling	
	protected function checkError($data) {

		$ret = Array();
		$error=false;
		foreach($data as $uidData) {
			
			// orkut specific
			if(isset($uidData['error']))
			{
				$error=true;

				// append captcha
				if(isset($uidData['error']['data']) && isset($uidData['error']['data']['captchaToken'])) {
					$captchaToken = $uidData['error']['data']['captchaToken'];
					$captchaUrl = 'captcha.php'.'?captchaUrl='. $uidData['error']['data']['captchaUrl'];
					$ret = array('id'=>'2', 'message'=>'captcha!', 'captchaToken'=>$captchaToken,'captchaUrl'=>$captchaUrl);
					break;
				}
				else {
					$ret = array('id'=>'1','message'=>'Error sending message');					
					break;
				}
			}
		}	

		if(!$error)
			$ret = array('id'=>'0','message'=>'ok');

		return $ret;
	}
}

?>
