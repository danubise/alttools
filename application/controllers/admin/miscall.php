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
    public function addcomment() {
        //Array
        //(
        //    [data] => Array
        //        (
        //            [comment] => sdfsdf
        //            [src] => 79002333150
        //        )
        //
        //    [addcomment] => Сохранить
        //)
        $this->db->delete("from `comments` where `src`='".$_POST['data']['src']."'");
        $this->db->insert("comments", $_POST['data']);
        $this->index();

    }
    public function send() {
        sendMiscallReport();
    }
    public function logout() {
        $this->user_model->logout();
        header('Location: '.baseurl());
    }
}
