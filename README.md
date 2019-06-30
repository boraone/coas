# Simple PHP wrapper to work with TCU api
How to use;

Install through composer 

    composer require coas/tcu  

Using the package

    Use Coas/TCU/TCU;

    $tcu = new TCU(username, token, instutionCode, inJson);
    
    username ( string ) = TCU username
    token ( string ) = TCU secret token credential
    instutionCode ( string ) = Institute code or null ( defaults to username )
    inJson ( bool ) = Tells to return results as a json object, set to false return the original xml response

    //Example
    $response = $tcu->checkStatus(‘indexNumber’); // Returns XML response as a json object 

if you wish to return the original XML

    $tcu->inJson(false);

or pass the fourth parameter as false

    $tcu = new TCU(username, token, null, false);

Response 

    $response = $tcu->add(category,indexF4, indexF6, otherForm4, otherFormSix );

    $status = $response->RESPONSE->RESPONSEPARAMETERS->STATUS;
    $code = $response->RESPONSE->RESPONSEPARAMETERS->ERROR_CODE;
    $description = $response->RESPONSE->RESPONSEPARAMETERS->STATUS_DESCRIPTION;

Methods
All methods as defined in the original TCU document API
