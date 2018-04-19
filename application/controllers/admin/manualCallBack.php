<?php

class ManualCallBack extends Core_controller {
    public function __construct() {
        parent::__construct();
        $this->module_name = 'Обзвон список CallBack';
    }
    public function start() {
        $this->db->update('settings',array("value"=>1),"`key`='manualCallBack'");
        $this->db->delete("from `manual_callback`");
        $this->index();
    }
    public function stop() {
        $this->db->update('settings',array("value"=>0),"`key`='manualCallBack'");
        $this->index();
    }

    public function index() {
        $settings = getSettings();
        $dialStatistic = $this->db->select("* from `manual_callback`");
        $this->view(
                array(
                    'view' => 'manualCallBack/index',
                    'var' => array(
                        'settings' => $settings,
                        'dialStatistic' => $dialStatistic
                    )
                )
            );
    }
}