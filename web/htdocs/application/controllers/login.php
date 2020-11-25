<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class login extends CI_Controller {

    /**
     * @brief	__construct : 생성자
     */
    public function __construct() {
        parent::__construct();
        //$this->output->enable_profiler(TRUE);
    }

	/**
     * @brief   index : 메인
     */
	public function index()	{
		header('Location:'.SITE_URL.'login/login');
		exit;
	}

	/**
     * @brief   login : 로그인
     */
    public function login() {
		$this->load->view('login/login');
	}

	/**
     * @brief   join : 회원가입
     */
    public function join() {
		$this->load->view('login/join');
	}
}
