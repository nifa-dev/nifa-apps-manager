<?php
namespace NifaAppsManager\Http;

use Cake\Http\Client;
use Cake\Log\Log;
use Cake\Orm\TableRegistry;
use NifaAppsManager\Traits\ApplicationDetailsTrait;

class NifaHttpClient extends Client {

    use ApplicationDetailsTrait;

    private $_application;

    public function __construct($applicationSystemDesignator) {

            $this->_application = $this->getSystemApp($applicationSystemDesignator);
    }

    /*
     * get
     *
     * will perform http get requests
     * auth variable if true will add Authorization header using the token from the application set
     * This function sends a get request, if the response is expired token then it will post a request to hte
     * client url (without any additions) in order ask for a renewal.  if the renewal is successful, it will
     * return the response.  If the renewal also fails, the failed response is returned.
     */
    public function get($urlAddition, $data = [], array $options = [], $auth = false) {

        //if there isnt a leading slash on the urlAddition, lets add it
        if(substr($urlAddition, 0, 1) != "/") $urlAddition = "/" . $urlAddition;

        //if Auth is set to true, let's add the authorization header with the application's client token
        if($auth) {
            $options['headers']['Authorization'] = 'Bearer ' . $this->_application->client_token;
        }

        //Log::write('debug', sprintf('Using Application %s, %s', $this->_application->id, $this->_application->name));
        //Log::write('debug', $this->_application);

        $queryString = "";
        if(count($data > 0)) {
            $c = 0;
            $queryString.="?";

            foreach($data as $field => $value) {
                if($c > 0) $queryString.= "&";
                $queryString.= $field . "=" . $value;
                $c++;
            }
        }
        //make initial request
        $http = new Client();
        $fullRequestUrl = $this->_application->client_url . $urlAddition . $queryString;
        Log::write('debug', sprintf("Get Request to: %s being sent.", $fullRequestUrl));
        Log::write('debug', 'with options' . json_encode($options));
        $response = $http->get($fullRequestUrl, [], $options);
        Log::write('debug', sprintf('The initial response is code %s', $response->code));
        $body = $response->json;
        Log::write('debug', 'with body: ' . json_encode($body));
        //if initial response is 500, theres a good chance its just expired token (message:"Expired token"):
        if(($response->code == 500) && (strtolower($body['message']) == "expired token")) {
            Log::write('debug', 'Request returned 500 and Expired Token, trying to renew token');

            //in order to make the renewal request we'll submit a request to the base url with the username and pass word as post data
            $credentials = ['public_key' => $this->_application->client_public_key, 'secret_key_hashed' => $this->_application->client_secret_key];
            $renewalResponse = $http->post($this->_application->client_url,
                json_encode($credentials),
                [
                    'headers' => [
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json'
                    ],
                    'type' => 'json'
                ]);

            //if response is ok, we'll save the application's token
            if($renewalResponse->isOk()) {
                //save the new token
                $body = $renewalResponse->json;
                if($body['success']) {
                    $this->_application->client_token = $body['data']['token'];
                    $applicationsTable = TableRegistry::get('Applications');
                    if($applicationsTable->save($this->_application)) {
                        Log::write('debug', 'Renewal was successful, token is saved: ' . $body['data']['token']);
                    } else {
                        Log::write('debug', 'Renewal was successful, token was not saved though.');
                    }

                    //now we need to use that token to execute the request again

                    $options['headers']['Authorization'] = "Bearer " . $body['data']['token'];
                    $response = $http->get($this->_application->client_url . $urlAddition, $data, $options);

                    //Log::write('debug', sprintf('After renewal, request was sent and responded with %s', $response->code));

                }
            }
        }
        //Log::write('debug', $options);
        //Log::write('debug', 'Final Response: ' . json_encode($response->json));
        //if response is anything else then lets just return that response
        return $response;
    }

    /*
     * post
     *
     * will perform http post requests
     * auth variable if true will add Authorization header using the token from the application set
     * This function sends a get request, if the response is expired token then it will post a request to hte
     * client url (without any additions) in order ask for a renewal.  if the renewal is successful, it will
     * return the response.  If the renewal also fails, the failed response is returned.
     */
    public function post($urlAddition, $data = [], array $options = [], $auth = false) {

        //if there isnt a leading slash on the urlAddition, lets add it
        if(substr($urlAddition, 0, 1) != "/") $urlAddition = "/" . $urlAddition;

        //if Auth is set to true, let's add the authorization header with the application's client token
        if($auth) {
            $options['headers']['Authorization'] = 'Bearer ' . $this->_application->client_token;
        }

        //Log::write('debug', sprintf('Using Application %s, %s', $this->_application->id, $this->_application->name));
        //Log::write('debug', $this->_application);

        //make initial request
        $http = new Client();
        $response = $http->post($this->_application->client_url . $urlAddition, $data, $options);
        $body = $response->json;
        //if initial response is 500, theres a good chance its just expired token (message:"Expired token"):
        if(($response->code == 500) && (strtolower($body['message']) == "expired token")) {
            Log::write('debug', 'Request returned 500 and Expired Token, trying to renew token');

            //in order to make the renewal request we'll submit a request to the base url with the username and pass word as post data
            $credentials = ['public_key' => $this->_application->client_public_key, 'secret_key_hashed' => $this->_application->client_secret_key];
            $renewalResponse = $http->post($this->_application->client_url,
                json_encode($credentials),
                [
                    'headers' => [
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json'
                    ],
                    'type' => 'json'
                ]);

            //if response is ok, we'll save the application's token
            if($renewalResponse->isOk()) {
                //save the new token
                $body = $renewalResponse->json;
                if($body['success']) {
                    $this->_application->client_token = $body['data']['token'];
                    $applicationsTable = TableRegistry::get('Applications');
                    if($applicationsTable->save($this->_application)) {
                        Log::write('debug', 'Renewal was successful, token is saved: ' . $body['data']['token']);
                    } else {
                        Log::write('debug', 'Renewal was successful, token was not saved though.');
                    }

                    //now we need to use that token to execute the request again

                    $options['headers']['Authorization'] = "Bearer " . $body['data']['token'];
                    $response = $http->get($this->_application->client_url . $urlAddition, $data, $options);

                    //Log::write('debug', sprintf('After renewal, request was sent and responded with %s', $response->code));

                }
            }
        }
        //Log::write('debug', $options);
        //Log::write('debug', 'Final Response: ' . json_encode($response->json));
        //if response is anything else then lets just return that response
        return $response;
    }

}