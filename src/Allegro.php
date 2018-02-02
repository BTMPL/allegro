<?php
namespace Allegro;

class Allegro
{
    /**
     * @var \SoapClient
     */
    protected $client = NULL;
    protected $session = NULL;

    protected $versionKeys = array();

    protected $key;
    protected $countryId;

    /**
     * Allegro
     * 
     * @param string $key
     * @param int $countryId
     * @param string $wsdl
     * @param array $options
     */
    public function __construct($key, $countryId = 1, $wsdl = 'https://webapi.allegro.pl/service.php?wsdl', $options = [])
    {
        $this->key = $key;
        $this->countryId = $countryId;

        $options = $this->setDefaultOptions($options);

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

    public function login($login, $password)
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
    
    /**
     * Logowanie za pomoca access tokena zwroconego przez REST API
     * 
     * @param string $token
     */
    public function loginWithAccessToken($token)
    {
        $request = array(
            'accessToken' => $token,
            'countryCode' => $this->countryId,
            'webapiKey' => $this->key,
        );

        $this->session = $this->client->doLoginWithAccessToken($request);
    }

    /**
     * Pobierz klienta SoapClient
     * 
     * Umozliwienie wywolania metod bezargumentowych 
     * (bez dodawania elementow sesjii allegro) 
     * np.: $Allegro->client()->__getLastRequest();
     * 
     * @return \SoapClient
     */
    public function client()
    {
        return $this->client;
    }

    public function __call($name, $arguments)
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

    private function setDefaultOptions($options)
    {
        if (empty($options['features'])) {
            $options['features'] = SOAP_SINGLE_ELEMENT_ARRAYS;
        }
        if (empty($options['trace'])) {
            $options['trace'] = true;
        }
        return $options;
    }
}
