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

/**
===========================================================================================================================
IMPORTANT NOTES, READ FIRST:
- This file simulates 3lo authentication process (as a browser).
- It stores a temporary cookie on your machine, used by curl. You need to have php-curl installed, with safe_mode disabled,
which means flag FOLLOWLOCATION must be available.
- This is not a standard feature, and was created in order to facilitate tests without having to authenticate all the time.
- It is not guaranteed that will work all the time. A minor change will break this process.
- Use this carefully. Making an user typing his/her name and password to authenticate on orkut is subjected to security issues.
- Consult Google first, if you intend to use on production.
============================================================================================================================


Process explained:

1- Open 'url' and Google will issue two 302.
    First: UniversalLogin
    Second: ServiceLogin

    Since we do not have cookies enabled, this will require us to inject username and password. 
    On this step, i´m using phpquery to extract form action, and all hidden fields, so we can pack and post.

2- Post all information described on step 2, to the action url (something like ServiceLoginAuth). It will issue another 302
    pointing you to a CheckCookie page. After that, a meta tag or script will make you redirect again

3- Extract url inside a meta tag, and issue a get. It will issue another 302, pointing you to an OAuthAuthorizeToken page again.
    This page contains a form which will ask you to authorize your app to have access.
    
4- Do the same step described on 1. Grab action, pack all hidden fields, and post to the server. After posting, Google will issue
    another 302, but this time to our callback page, passing oauth_verifier and oauth_token back. 
    
5- That´s it! now we are ready to send our 3lo rpc to Orkut :)
*/

// curl wrapper
require "class.curl.php";

// phpquery to walk on html tree
require "phpquery.php";

function getKeys($url, $user, $pass)
{

    /**
     STEP 1 - open our initial url. Grab form data
    */
    
    // kill cookies first
    $cookieFile=g_base_dir.'/cookies.txt';
    unlink($cookieFile);
    
    $curl = new cURL($cookieFile);
    $r = $curl->get($url);


    // instance phpquery
    phpQuery::newDocumentHTML($r);

    $form = pq('#gaia_loginform');
    $action = $form->attr('action');
    $postData = Array();

    foreach($form->find('input[type=hidden]') as $input)
    {
        $input = pq($input);
        $postData[$input->attr('name')] = $input->attr('value');
    }

    
    $postData['Email'] = $user;
    $postData['Passwd'] = $pass;
    $postData['PersistentCookie'] = 'no';
    $postData['signIn'] = 'Login';

    /**
    STEP 2 - Post everything back
    */
    $post = $curl->post($action, $postData);
        

    /**
    STEP 3 - Extract url from meta tag, and issue a get
    */

    // meta redirect
    preg_match('/url=[^>]+/', $post, $matches);
    $url = str_replace('url=&#39;','',$matches[0]);
    $url = str_replace('&#39;"','',$url);
    $url = str_replace('&amp;','&',$url);


    $final = $curl->get($url);

    /**
    STEP 4 - Almost there. Extract all form and hidden fields, passing authorization back to the server.
    This will redirect to our callback_direct.php
    */

    phpQuery::newDocumentHTML($final);

    $form = pq('form');
    $action = 'https://www.google.com/accounts/'.$form->attr('action');
    $postData = Array();

    foreach($form->find('input[type=hidden]') as $input)
    {   
        $input = pq($input);
        $postData[$input->attr('name')] = $input->attr('value');
    }

    $postData['allow']='Conceder acesso';
    $post = $curl->post($action, $postData);

    
    // ok, posted, now parsing verifier and token to inject on our _GET dict.
    parse_str($post, $ret);

    // fix and inject
    $_GET['oauth_verifier'] = str_replace(' ', '+', $ret['oauth_verifier']);
    $_GET['oauth_token'] = $ret['oauth_token'];

    // that is it.
    // tks Tamper Data for allowing this :)
}
?>