<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class calendar extends CI_Controller {

    /**
     * @brief	__construct : 생성자
     */
    public function __construct() {
        parent::__construct();
        //$this->output->enable_profiler(TRUE);
    }

	/**
     * @brief   index : 캘린더
     */
	public function index()	{
		$this->load->view('calendar/calendar');
    }

	/**
     * @brief   procrastina : 물주기 미루기 페이지
     */
	public function procrastina()	{
		$this->load->view('calendar/procrastina');
    }
}
