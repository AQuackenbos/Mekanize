<?php 

require_once('require.php');

initRequest();

function initRequest()
{
	$_parameters = explode('/',trim($_SERVER['REQUEST_URI'],' /'));
	
	$_requestType = $_parameters[1];
	$_query = array();
	
	$idSearch = false;
	//0 = 'api'

	if($_SERVER['QUERY_STRING'])
	{
		parse_str($_SERVER['QUERY_STRING'],$_query);
		$idSearch = false;
	}

	if(stripos($_requestType,'?')!==false)
	{
		$split = explode('?',$_requestType);
		$_requestType = reset($split);
		$idSearch = false;
	}

	if(empty($_query) && count($_parameters) > 2)
	{
		$_query['id'] = $_parameters[2];
		$idSearch = true;
	}

	$acceptedRequests = array('characters','episodes','locations','organizations');

	if(!in_array($_requestType,$acceptedRequests))
	{
		echo json_encode('{}');
		exit;
	}
	
	_getData($_requestType,$_query,$idSearch);
}

function _getMap($type)
{
	$maps = array(
		'character' => array(
			'id' => 'ROW:id',
			'name' => 'ROW:name',
			'desc_short' => 'ROW:description_short',
			'desc_loon' => 'ROW:description_full',
			'first_seen_id' => 'RELATION_SINGLE:relation_firstsightings',
			'episode_ids' => 'RELATION:relation_episodes_characters:episode_id ASC',
			'preview_image' => 'IMAGES:preview',
			'main_image' => 'IMAGES:main',
			'images' => 'IMAGES:gallery',
			'background_images' => 'IMAGES:background',
		),
		'episode' => array(
			'id'	=> 'ROW:id',
			'title' => 'ROW:title',
			'desc_short' => 'ROW:description_short',
			'desc_long' => 'ROW:description_full',
			'season' => 'ROW:season',
			'episode' => 'ROW:season_episode',
			'preview_image' => 'IMAGES:preview',
			'main_image' => 'IMAGES:main',
			'images' => 'IMAGES:gallery',
			'background_images' => 'IMAGES:background',
			'first_sight_ids' => 'RELATION:relation_firstsightings',
			'character_ids' => 'RELATION:relation_episodes_characters:character_id ASC'
		),
		'organization' => array(),
		'location' => array()
	);
	
	return $maps[$type];
}

function _output($wrappedObject)
{
	echo json_encode($wrappedObject);
}

function _mapData($type,$raw)
{
	$single = _getSingular($type);
	$map = _getMap($single);
	$object = array();
	
	foreach($map as $objectKey => $value)
	{
		@list($location,$relevant,$sort) = explode(':',$value);
		
		switch($location)
		{
			case 'ROW':
				$object[$objectKey] = $raw[$relevant];
				break;
			case 'RELATION':
			case 'RELATION_SINGLE':
				$params = array(
					$single.'_id' => $raw['id'],
				);
				
				$result = _getDbResults($relevant,$params,$sort);
				
				$data = array();
				
				while($row = $result->fetch(PDO::FETCH_ASSOC))
				{
					$colCount = count($row);
					//if 2 => Assume its a core relation and just store values
					if($colCount == 2)
					{
						foreach($row as $key=>$val)
						{
							if($key == $single.'_id') continue;
							if($location == 'RELATION_SINGLE')
							{
								$object[$objectKey] = intval($val);
							}
							else
							{
								$data[] = intval($val);
							}
						}
					}
					//otherwise, dump the whole object into the results
					else
					{
						$data[] = $row;
					}
				}
				
				if($location == 'RELATION')
					$object[$objectKey] = $data;
				
				break;
			case 'IMAGES':
				$params = array(
					'related_id_type' => $type,
					'related_id' => $raw['id'],
					'type' => $relevant
				);
				
				$result = _getDbResults('images',$params,$sort);
				$data = array();
				
				while($row = $result->fetch(PDO::FETCH_ASSOC))
				{
					$data[] = $row['url'];
				}
				
				$object[$objectKey] = implode(',',$data);
				break;
		}
	}
	
	return $object;
}

function _getSingular($type)
{
	//assume [^single]s for now
	return trim($type,'s');
}

function _getData($type,$params,$single)
{
	$dbResults = _getDbResults($type,$params);
	
	if($single)
	{
		$row = $dbResults->fetch(PDO::FETCH_ASSOC);
		
		$object = _mapData($type,$row);
		
		$wrapper = array(_getSingular($type) => $object);
	}
	else
	{
		$wrapper = array($type => array());
		
		while($row = $dbResults->fetch(PDO::FETCH_ASSOC))
		{
			$object = _mapData($type,$row);
			
			$wrapper[$type][] = $object;
		}
	}
	
	_output($wrapper);
}

?>