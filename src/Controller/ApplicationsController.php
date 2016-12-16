<?php
namespace NifaAppsManager\Controller;

use App\Controller\AppController;
use NifaAppsManager\Traits\ApplicationDetailsTrait;

/**
 * Applications Controller
 *
 * @property \App\Model\Table\ApplicationsTable $Applications
 */
class ApplicationsController extends AppController
{

    use ApplicationDetailsTrait;

    public function initialize() {
        parent::initialize();
        $this->Auth->allow(['makeRequest', 'token']);

    }


    public function token()
    {
        

        $user = $this->Auth->identify();
        if (!$user) {
            throw new UnauthorizedException('Invalid username or password');
        }

        $this->set([
            'success' => true,
            'data' => [
                'token' => JWT::encode([
                    'sub' => $user['id'],
                    'exp' =>  time() + 604800
                ],
                    Security::salt())
            ],
            '_serialize' => ['success', 'data']
        ]);
    }


    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $applications = $this->paginate($this->Applications);

        $this->set(compact('applications'));
        $this->set('_serialize', ['applications']);
    }

    /**
     * View method
     *
     * @param string|null $id Application id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $application = $this->Applications->get($id, [
            'contain' => []
        ]);

        $this->set('application', $application);
        $this->set('_serialize', ['application']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    /*public function add()
    {
        $application = $this->Applications->newEntity();
        if ($this->request->is('post')) {
            $application = $this->Applications->patchEntity($application, $this->request->data);
            $result = $this->Applications->save($application);
            if(is_object($result)) {
                $this->Flash->success(__('The application has been saved.'));
                $this->redirect(['action' => 'index']);
            } else {
                $error = 'The application could not be saved. Please, try again.';
                if(is_array($result)) {
                    if(array_key_exists('message', $result)) $error = $result['message'];

                }
                $this->Flash->error(__($error));

            }
        }
        $this->set(compact('application'));
        $this->set('_serialize', ['application']);
    }*/

    /**
     * Edit method
     *
     * @param string|null $id Application id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $application = $this->Applications->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $application = $this->Applications->patchEntity($application, $this->request->data);
            $result = $this->Applications->save($application);
            if(is_object($result)) {
                $this->Flash->success(__('The application has been saved.'));
                $this->redirect(['action' => 'index']);
            } else {
                $error = 'The application could not be saved. Please, try again.';
                if(is_array($result)) {
                    if(array_key_exists('message', $result)) $error = $result['message'];

                }
                $this->Flash->error(__($error));

            }

        }
        $this->set(compact('application', 'result'));
        $this->set('_serialize', ['application']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Application id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    /*public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $application = $this->Applications->get($id);
        $result = $this->Applications->delete($application);

        if(is_array($result)) {
            $this->Flash->error(__($result['message']));
        } else if ($result === false) {
            $this->Flash->error(__('The application could not be deleted. Please, try again.'));
        } else {

            $this->Flash->success(__('The application has been deleted.'));
        }

        return $this->redirect(['action' => 'index']);
    }*/

    public function makeRequest($applicationSystemDesignator = "contest") {

        if($url = $this->request->query('url')) {

            $data = $this->request->query;
            $request = ['url' => $url, 'data' => $data];
            unset($data['url']);
            $result = $this->Applications->makeNifaHttpRequest($url, $data, $applicationSystemDesignator, true);


        } else {
            $result = ['status' => 'fail', 'error' => 'The url was not supplied', 'data' => $this->request->data];
        }

        $this->set(compact('result', 'request'));
        $this->set('_serialize', ['result']);

    }

    public function regenerateKeys($id) {
        $application = $this->Applications->get($id);
        if($this->Applications->regenerateKeys($application)) {
            $this->Flash->success(__('The application keys have been regenerated.  You can find the new keys below.  You will have to make changes in any client app(s).'));
            $this->redirect(['action' => 'view', $id]);
        } else {
            $this->Flash->error(__('The application keys could not be regenerated. Please, try again.'));
            $this->redirect(['action' => 'view', $id]);
        }
    }
}
