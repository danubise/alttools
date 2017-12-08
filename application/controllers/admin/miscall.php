<?php

class Miscall extends Core_controller {
    public function __construct() {
        parent::__construct();
        $this->module_name = 'Пропущенные Звонки';
    }

    public function index() {
    $lastMiscallCDR = getMiscallReport(false);
    $this->view(
            array(
                'view' => 'miscall/make',
                'var' => array(
                    'lastMiscallCDR' => $lastMiscallCDR
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
    public function send() {
        sendMiscallReport();
    }
    public function logout() {
        $this->user_model->logout();
        header('Location: '.baseurl());
    }
}
