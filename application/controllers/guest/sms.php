<?php

class Sms extends Core_controller {
    public function __construct() {
        parent::__construct();
        $this->module_name = 'Отправка sms';
    }

    public function index() {

    $this->view(
            array(
                'view' => 'smsui',
                'var' => array(

                )
            )
        );
    }

    public function logout() {
        $this->user_model->logout();
        header('Location: '.baseurl());
    }
}
