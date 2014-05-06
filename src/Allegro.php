<?

namespace Allegro;

class Allegro {

    private $_client = NULL;
    private $_session = NULL;
    
    private $versionKeys = array();
	
	private $key;
	private $countryId;

    function __construct($key, $countryId = 1) {
        
        $options = array();
        $options['features'] = SOAP_SINGLE_ELEMENT_ARRAYS;
        $this->_client = new \SoapClient('https://webapi.allegro.pl/service.php?wsdl', $options);
        $request = array(
            'countryId' => $countryId,
            'webapiKey' => $key
        );
		
		$this->key = $key;
		$this->countryId = $countryId;
        
        $sys = $this->_client->doQueryAllSysStatus($request);
        foreach ($sys->sysCountryStatus->item as $row) {
            $this->versionKeys[$row->countryId] = $row;
        }
    }

    function login($login, $password) {
        $request = array(
            'userLogin' => $login,
            'userPassword' => $password,
            'countryCode' => $this->countryId,
            'webapiKey' => $this->key,
            'localVersion' => $this->versionKeys[$this->countryId]->verKey,
        );
        $this->_session = $this->_client->doLogin($request);
    }

    function __call($name, $arguments) {
        if(isset($arguments[0])) $arguments = (array) $arguments[0];
        else $arguments = array();
        
        $arguments['sessionId'] = $this->_session->sessionHandlePart;
        $arguments['sessionHandle'] = $this->_session->sessionHandlePart;
        $arguments['webapiKey'] = $this->key;
        $arguments['countryId'] = $this->countryId;
        $arguments['countryCode'] = $this->countryId;

        return $this->_client->$name($arguments);
    }
    
}