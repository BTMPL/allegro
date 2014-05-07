Allegro
=======

Prosty wrapper do obsługi WebAPI Allegro.

Instalacja
=======

Dostępny przez Composer:

    "require": {
        "btmpl/allegro": "dev-master"
    },

następnie

    $ composer update
  
Wykorzystanie
=======

    $allegro = new \Allegro\Allegro('KLUCZ');
    $allegro->login('LOGIN', 'HASLO');
  
Do konstruktora, jako opcjonalny parametr przekazać można dodatkowo ID kraju, oraz adres innego WSDL (np. testowego).

Aby wywołać metodę api, np [doGetPostBuyFormsDataForSellers](http://allegro.pl/webapi/documentation.php/show/id,703) używamy wywołania:

    $response = $allegro->doGetPostBuyFormsDataForSellers($payload);
    
Skrypt automatycznie dołącza `sessionId`, `sessionHandle`, `webapiKey`, `countryId` i `countryCode` (ponieważ konsekwencja nie jest silną stroną API Allegro).
