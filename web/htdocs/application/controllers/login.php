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
  
	/**
   * @brief   user : 플랜트 회원 약관(필수)
   */
  public function user() {
    $this->load->view('login/user.html');
  }

  /**
     * @brief   personal : 개인정보 수집/이용(필수)
     */
    public function personal() {
    $this->load->view('login/personal.html');
  }

  /**
     * @brief   service : 플랜트 서비스 약관(필수)
     */
    public function service() {
    $this->load->view('login/service.html');
  }

  
  /**
     * @brief   transaction : 전자금융 거래 이용 약관(필수)
     */
    public function transaction() {
    $this->load->view('login/transaction.html');
  }
}
