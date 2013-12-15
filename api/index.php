<?php 
define('MEK_API',true);
require_once('_database.php');

class Api {

	protected $_idSearch;
	protected $_query;
	protected static $_acceptedRequests = array(
		'meks' => 'MekController'
	);
	
	public function initRequest()
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

		if(!in_array($_requestType,$this->_acceptedRequests))
		{
			return false;
		}
		
		$this->_idSearch = $idSearch;
		$this->_query = $_query;
		return $this->_acceptedRequests[$_requestType];
	}

	public function getQuery()
	{
		return $this->_query;
	}

	public function isIdSearch()
	{
		return $this->_idSearch;
	}
}


$controller = new Api()->initRequest();
if($controller === false)
{
	echo '{"error":"Bad Request"}';
	exit;
}
require_once('api/Controllers/AbstractController.php');
require_once('api/Controllers/'.$controller.'.php');
echo json_encode(new $controller($api->getQuery(),$api->isIdSearch())->getResultObject());
exit;