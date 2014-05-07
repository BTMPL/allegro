<?
namespace Allegro;

class Allegro 
{
    protected $client = NULL;
    protected $session = NULL;

    protected $versionKeys = array();

    protected $key;
    protected $countryId;

    function __construct($key, $countryId = 1, $wsdl = 'https://webapi.allegro.pl/service.php?wsdl') 
    {
        $this->key = $key;
        $this->countryId = $countryId;
        
        $options = array();
        $options['features'] = SOAP_SINGLE_ELEMENT_ARRAYS;
        $options['trace'] = true;
        $this->client = new \SoapClient($wsdl, $options);
        $request = array(
            'countryId' => $countryId,
            'webapiKey' => $key
        );


        $status = $this->client->doQueryAllSysStatus($request);
        foreach ($status->sysCountryStatus->item as $row) {
            $this->versionKeys[$row->countryId] = $row;
        }
    }

    function login($login, $password) 
    {
        $request = array(
            'userLogin' => $login,
            'userPassword' => $password,
            'countryCode' => $this->countryId,
            'webapiKey' => $this->key,
            'localVersion' => $this->versionKeys[$this->countryId]->verKey,
        );
        $this->session = $this->client->doLogin($request);
    }

    function __call($name, $arguments) 
    {
        if(isset($arguments[0])) $arguments = (array)$arguments[0];
        else $arguments = array();

        $arguments['sessionId'] = $this->session->sessionHandlePart;
        $arguments['sessionHandle'] = $this->session->sessionHandlePart;
        $arguments['webapiKey'] = $this->key;
        $arguments['countryId'] = $this->countryId;
        $arguments['countryCode'] = $this->countryId;

        return $this->client->$name($arguments);
    }

}