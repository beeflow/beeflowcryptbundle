### BeeflowCryptBundle ###
Encrypt and decrypt REST communication for Symfony

### Bundle installation ###
To install bundle add to your composer.json file:

    "repositories": [
        {
            "type": "vcs",
            "url":  "https://beeflow@bitbucket.org/beeflowteam/beeflowcryptbundle.git"
        }
    ],
    "require": {
        ...
        "beeflow/beeflowcryptbundle": "dev-master"
    }

and then

    $ composer update && php app/console doctrine:schema:update --force

and finally add to `app/AppKernel.php` file:
    
    public function registerBundles()
    {
        ...
        new Beeflow\BeeflowCryptBundle\BeeflowCryptBundle(),
    }
    
### New API client ###

    $ php app/console beeflow:crypt:client:create "API Key" "Client name" "encryption method (ex. AES256)" /some/path/to/your/cert
 
### Deleting client ###

    $ php app/console beeflow:crypt:client:delete "API Key"

### New certificate for existing client ###

    $ php app/console beeflow:crypt:cert:install "API Key" "encryption method  (np. AES256)" /some/path/to/your/cert
   
    
### Encrypting and decrypting data ###
When you send encrypted data to your backend, listener will take this data, decrypt and send to original controller. The same listener will encrypt original response so you do not have to do anything else.

##### request #####
    {
        api_key: "yuefhw87y4w47thie5844", 
        encrypted_request: "xrxx/NzhikVSMFu09EBju7YWsomaGhrAvsNZM313QCg=" 
    }
    
##### response #####

    {
        "encrypted_response" : "rERokl3ydHThr51vvY5rfmc\/UP+INn3Ndj\/Jhnwx068="
    }
