<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class myplant extends CI_Controller {

    /**
     * @brief	__construct : 생성자
     */
    public function __construct() {
        parent::__construct();
        //$this->output->enable_profiler(TRUE);
	}
	
	/**
     * @brief	index : 인덱스
     */
	public function index()	{
		header('Location:'.SITE_URL.'myplant/list');
	}

	/**
     * @brief	index : 리스트
     */
	public function list()	{
		$this->load->view('myplant/list');
	}
	
	/**
     * @brief   detail : 상세
     */
	public function detail() {
		$this->load->view('myplant/detail');
	}
	
	/**
     * @brief   index : 인덱스
     */
	public function form() {
		$this->load->view('myplant/form');
	}
}