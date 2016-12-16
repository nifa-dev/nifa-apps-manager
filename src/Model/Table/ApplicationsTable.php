<?php
namespace NifaAppsManager\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Event\Event;
use Cake\Utility\Text;
use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\Http\Client;
use Cake\Log\Log;

use NifaAppsManager\Http\NifaHttpClient;

/**
 * Applications Model
 *
 * @method \App\Model\Entity\Application get($primaryKey, $options = [])
 * @method \App\Model\Entity\Application newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Application[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Application|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Application patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Application[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Application findOrCreate($search, callable $callback = null)
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ApplicationsTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('applications');
        $this->displayField('name');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->requirePresence('name', 'create')
            ->notEmpty('name');

        $validator
            ->requirePresence('client_public_key', 'create')
            ->notEmpty('client_public_key');

        $validator
            ->requirePresence('client_secret_key', 'create')
            ->notEmpty('client_secret_key');

        $validator
            ->requirePresence('client_url', 'create')
            ->notEmpty('client_url')
            ->url('client_url');

        /*$validator
            ->requirePresence('secret_key_hashed', 'create')
            ->notEmpty('secret_key_hashed');*/

        $validator
            ->boolean('inactive')
            ->requirePresence('inactive', 'create')
            ->notEmpty('inactive');

        return $validator;
    }

    public function makeNifaHttpRequest($urlAddition, $data, $application = null, $auth = false) {
        $http = new NifaHttpClient($application);
        $response = $http->get($urlAddition, $data,
            ['headers' => ['Accept' => 'application/json']], $auth);
        if($response->isOk()) {
            return ['status' => 'success', 'data' => $response->json, 'url' => $urlAddition, 'dataProvided' => $data, 'code' => $response->code];

        }
        return ['status' => 'error', 'message' => 'The request for data failed', 'code' => $response->code];
    }

    public function beforeSave(Event $event) {
        Log::write('debug', 'Yes, before save executed');
        $entity = $event->data['entity'];
        if($entity->isNew()) {
            $entity->accessible('host_public_key', true);
            $entity->accessible('host_secret_key', true);
            $entity->accessible('host_secret_key_hashed', true);   
            $entity->host_public_key = Text::uuid();
            $entity->host_secret_key = Text::uuid();
            $entity->host_secret_key_hashed = $entity->host_secret_key;
        }

        //if client information set, try to contact the client
        if(($entity->client_public_key) && ($entity->client_private_key) && ($entity->client_url)) {
            
            $http = new Client();

            $credentials = ['public_key' => $entity->client_public_key, 'secret_key_hashed' => $entity->client_secret_key];

            $response = $http->post($entity->client_url,
                json_encode($credentials),
                [
                   'headers' => [
                       'Accept' => 'application/json',
                       'Content-Type' => 'application/json'
                   ],
                    'type' => 'json'
                ]);

            /*Log::write('debug', $response);
            Log::write('debug', $response->isOk());
            Log::write('debug', $response->code);
            Log::write('debug', $event->result);*/
            if($response->isOk() === false) {
                $event->stopPropagation();
                //$event->result = 'connection refused, try again';
                //Log::write('debug', $event->result);
                return ['result' => false, 'message' => "Server connection refused, check your credentials and try again"];

            }

            $body = $response->json;
            if($body['success']) {
                $entity->client_token = $body['data']['token'];
            } else {
                //Log::write('debug', 'stopping here');
                $event->stopPropagation();
                return ['result' => false, 'message' => "Server returned a response in an unexpected format"];
            }
        }

        return true;
    }

    public function beforeDelete(Event $event, EntityInterface $entity, ArrayObject $options) {
        if($entity->system_designator) {
            $event->stopPropagation();
            return ['result' => false, 'message' => "This Application is critical to this module, it cannot be deleted."];
        }
    }

    public function regenerateKeys(EntityInterface $entity) {
        //turn off field guarding
        $entity->accessible('host_public_key', true);
        $entity->accessible('host_secret_key', true);
        $entity->set('host_public_key', Text::uuid());
        $entity->set('host_secret_key', Text::uuid());

        return $this->save($entity);
    }



}
