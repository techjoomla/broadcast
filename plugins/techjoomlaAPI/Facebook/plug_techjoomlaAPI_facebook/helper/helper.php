<?php

	jimport('joomla.html.html');
	jimport( 'joomla.plugin.helper' );
class plug_techjoomlaAPI_facebookHelper
{ 	
	
function plug_techjoomlaAPI_facebookRender_profile($profileData)
{
	
	$data = $profileData['profiledata'];
	$r_profileData=array();		
	$fbfields=$profileData['mapping_field'];
	//$fbfieldsA=array('first_name','middle_name','last_name','name','gender','email','work','location','hometown','bio','picture-url');

	foreach($fbfields as $key=>$arrkey)
	{
		if(is_array($data[$arrkey]))
		{
		$currentval=$this->populatearray($data[$arrkey]);
			$r_profileData[$arrkey]=$currentval;
		}
		else
		{
			if($arrkey=="gender" )
			$r_profileData[$arrkey]=ucwords($data[$arrkey]);
			else
			$r_profileData[$arrkey]=$data[$arrkey];
		}
	}
	if(isset($data['picture-url']))
	{
		$r_profileData['picture-url']=$data['picture-url'];
	}
	if(isset($data['birthday']))
	{
		$birthday=explode('/',$data['birthday']);
		$birthdaystr=$birthday[2].'-'.$birthday[0].'-'.$birthday[1];
		$birthdaystr=strtotime($birthdaystr);
		$birthdaystr=date('Y-m-d H:i:s',$birthdaystr);
		$r_profileData['birthday']=$birthdaystr;
	}
	if(isset($data['languages']))
	{
		
		
		foreach($data['languages'] as $key=>$value)
		{
			if($value['name'])
			$languages[]=$value['name'];
			
		}
		$r_profileData['languages']=implode(',',$languages);
	}
	
	if(isset($data['work']))
	{
		
		
		foreach($data['work'] as $key=>$value)
		{
			if($value['employer']['name'])
			$employer[]=$value['employer']['name'];
			
		}
		$r_profileData['work']=implode(',',$employer);
	}

	$r_profileData['education']='';

	if(isset($data['education']))
	{
		if(isset($data['education'][0]['concentration']))
		{
			$college=$data['education'][0]['concentration'];
			if($college)
			{
				$r_profileData['education']= $college[1]['name'];
			}
		}
		else if(isset($profileData['profiledata']['education']['0']['school']['name']))
		{
			$r_profileData['education']=$profileData['profiledata']['education']['0']['school']['name'];
		}
	}
	
	if(isset($data['current_location']['city']))
	{
					$r_profileData['city']=$data['current_location']['city'];
		
	}
	if(isset($data['current_location']['state']))
	{
			$r_profileData['state']=$data['current_location']['state'];
		
	}
	
	if(isset($data['current_location']['country']))
	{
					$r_profileData['country']=$data['current_location']['country'];
		
	}
	return $r_profileData;
}

	public function populatearray($profileData1)
	{

		$count=0;
		foreach ($profileData1 as $key=>$value)
		{
			if(is_array($value))
			{
				$returnval=$this->populatearray($value);
				if($returnval)
					return $returnval;
			}
			else if($key=='name')
			{
				return $value;
			}
		}
	}
	
	public function rendereducations($education)
	{
		$r_education='';
		foreach($education as $edukey=>$eduvalue)
			{
				if(trim($eduvalue['degree']['name']))
					{
						if(isset($r_education))					
							$r_education=$r_education.", ".$eduvalue['degree']['name']." ".$eduvalue['school']['name']."  \n";
						else
							$r_education=$eduvalue['degree']['name']." ".$eduvalue['school']['name']."  \n";
						
					}
			
			}
			return $r_education;
	
	}

}





