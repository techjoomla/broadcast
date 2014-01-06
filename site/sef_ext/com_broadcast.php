<?php

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

// ------------------  standard plugin initialize function - don't change ---------------------------
global $sh_LANG;
$sefConfig = & shRouter::shGetConfig();  
$shLangName = '';
$shLangIso = '';
$title = array();
$shItemidString = '';
$dosef = shInitializePlugin( $lang, $shLangName, $shLangIso, $option);
//if ($dosef == false) return;


// ------------------  standard plugin initialize function - don't change ---------------------------

// ------------------  load language file - adjust as needed ----------------------------------------
//$shLangIso = shLoadPluginLanguage( 'com_XXXXX', $shLangIso, '_SEF_SAMPLE_TEXT_STRING');
// ------------------  load language file - adjust as needed ----------------------------------------

$view = isset($view) ? @$view : null;
$layout = isset($layout) ? @$layout : null;
$controller = isset($controller) ? @$controller : null;


$Itemid = isset($Itemid) ? @$Itemid : null;
$shWeblinksName = shGetComponentPrefix($option);
$shWeblinksName = empty($shWeblinksName) ?  
	getMenuTitle($option, isset($view) ? $view:null, isset($Itemid) ? $Itemid:null, null, $shLangName) : $shWeblinksName;
$shWeblinksName = (empty($shWeblinksName) || $shWeblinksName == '/') ? 'broadcast':$shWeblinksName;
if (!empty($shWeblinksName)) $title[] = $shWeblinksName;

/*$title[]=$view;
if (isset($layout))
$title[]=$layout;*/
if(isset($task))
{
	if($task= 'get_access_token')
	$dosef=false;
}
if($view=='config')
{
		switch ($layout) {
			case 'default':
			break;
	

		}
}

if($controller=='broadcast')
{
	if(isset($task) )
	$title[] =$task;
}

if($controller=='rss')
{
	if(isset($task) )
	$title[] =$task;
}


shRemoveFromGETVarsList('option');
shRemoveFromGETVarsList('view');
shRemoveFromGETVarsList('controller');
shRemoveFromGETVarsList('task');
shRemoveFromGETVarsList('layout');
shRemoveFromGETVarsList('rout');
shRemoveFromGETVarsList('lang');

if (!empty($Itemid))
  shRemoveFromGETVarsList('Itemid');
if (!empty($limit))  
shRemoveFromGETVarsList('limit');
if (isset($limitstart)) 
  shRemoveFromGETVarsList('limitstart'); // limitstart can be zero
if (isset($id))   
  shRemoveFromGETVarsList('id');  
if (isset($controller))   
	shRemoveFromGETVarsList('controller'); 

 
 
   




       //   $title[] = $cid."-".videoseriesGetShortcode( $cid).".html";
             
// ------------------  standard plugin finalize function - don't change ---------------------------  
if ($dosef){
   $string = shFinalizePlugin( $string, $title, $shAppendString, $shItemidString, 
      (isset($limit) ? @$limit : null), (isset($limitstart) ? @$limitstart : null), 
      (isset($shLangName) ? @$shLangName : null));
}      
// ------------------  standard plugin finalize function - don't change ---------------------------
  
?>
