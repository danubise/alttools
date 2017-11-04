<?php

class Miscall extends Core_controller {
    public function __construct() {
        parent::__construct();
        $this->module_name = 'HLR Centre';
    }

    public function index() {
        $this->view(
            array(
                'view' => 'miscall/make',
                'var' => array(
                )
            )
        );
    }

    public function make($last) {
        $this->view(
            array(
                'view' => 'miscall/make',
                'var' => array()
            )
        );

    }

    public function logout() {
        $this->user_model->logout();
        header('Location: '.baseurl());
    }
}
