<?php
// src/Controller/UsersController.php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;


class UsersController extends AppController
{
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        // Allow users to register and logout.
        // You should not add the "login" action to allow list. Doing so would
        // cause problems with normal functioning of AuthComponent.
        $this->Auth->allow(['add', 'logout']);
    }
    public function home(){
        
    }
    public function index()
    {
        $this->set('users', $this->Users->find('all'));
    }

    public function view($id)
    {
        $user = $this->Users->get($id);
        $this->set(compact('user'));
    }
    public function add()
    {
        $user = $this->Users->newEntity();
        if ($this->request->is('post')) {
            // Prior to 3.4.0 $this->request->data() was used.
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if($user['role'] == 'intern'){
                if ($this->Users->save($user)) {
                    $interns = TableRegistry::get('Interns');
                    $intern = $interns->newEntity();
                    $intern->email = $user['email'];
                    $interns->save($intern);
                    $this->Flash->success(__('The user has been saved.'));
                    return $this->redirect(['action' => 'login']);
                }
               
            }else if($user['role'] == 'advisor'){
                if ($this->Users->save($user)){
                    $advisors = TableRegistry::get('Advisors');
                    $advisor = $advisors->newEntity();
                    $advisor->email = $user['email'];
                    $advisors->save($advisor);
                    $this->Flash->success(__('The user has been saved.'));
                    return $this->redirect(['action' => 'login']);
                }
            }
            $this->Flash->error(__('Unable to add the user.'));
            
        }
        $this->set('user', $user);
    }
    public function login()
    {
        if ($this->request->is('post')) {
            $user = $this->Auth->identify();
            if ($user) {
                $this->Auth->setUser($user);
                return $this->redirect($this->Auth->redirectUrl());
            }
            $this->Flash->error(__('Invalid email or password, try again'));
        }
    }
    public function logout()
    {
        return $this->redirect($this->Auth->logout());
    }

    public function profile(){
        $id = $this->Auth->user('id');
        $user = $this->Users->get($id, [
            'contain' => []
        ]);
        $this->set(compact('user'));

        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
    }


}

?>