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
     * @brief   duplicationCheck : 아이디 중복확인
     */
    public function duplicationCheck() {

		$email = $this->input->post('email',TRUE);

		$where = array(
			'email'	=>	$email,
			'del_yn'	=>	'N',
		);
		$this->db->where($where);
		$query = $this->db->get('user');
		$duplicationCheck = $query->num_rows();

		$result = array(
			'key' => 'duplicationCheck',
			'data' => array(
				'result' => $duplicationCheck
			)
		);
		echo json_encode($result);
	}

	/**
     * @brief   join : 회원가입
     */
    public function join() {
		$user_id = $this->input->post('user_id',TRUE);
		$user_password = $this->input->post('user_password',TRUE);
 
		$where = array(
			'user_id'	=>	$user_id,
			'del_yn'	=>	'N',
		);
		$this->db->where($where);
		$query = $this->db->get('user');
		$duplicationCheck = $query->num_rows();

		// 아이디 중복확인이 성공했을 경우
		if ($duplicationCheck == 0 ) {

			// 등록 배열
			$where = array(
				'user_id' => $user_id,
				'withdrawal_yn' => 'N',
				'del_yn' => 'N',
				'reg_date' => date('Ymd'),
				'reg_time' => date('His')
			);

			$this->db->trans_start();

			$this->db->set('user_password', 'password("'.$user_password.'")', FALSE );
			$this->db->set($where);
			$this->db->insert('user');
			
			$this->db->trans_complete();

			$result = array(
				'key' => '',
				'data' => array()
			);
			if ($this->db->trans_status() === TRUE) {
				$result['key'] = 'joinSuccess';
			} else {
				$result['key'] = 'joinFail';
			}
			echo json_encode($result);

		// 아이디 중복확인이 실패했을 경우
		} else {
			$result = array(
				'key' => 'duplicationCheckFail',
				'data' => array()
			);
			echo json_encode($result);
		}
	}

	/**
     * @brief   login : 로그인
     */
    public function login() {
		$user_id = $this->input->post('user_id',TRUE);
		$user_password = $this->input->post('user_password',TRUE);
 
		$where = array(
			'user_id'	=>	$user_id,
			'del_yn'	=>	'N',
		);
		$this->db->where($where);
		$this->db->where('user_password','password("'.$user_password.'")', FALSE);
		$query = $this->db->get('user');
		$loginCheck = $query->num_rows();

		// 아이디 비밀번호가 맞아 로그인 되었을 경우
		if ($loginCheck > 0 ) {
			$result = array(
				'key' => 'loginSuccess',
				'data' => array(
					'result' => $query->row()
				)
			);
			echo json_encode($result);
			
		// 아이디 비밀번호가 맞지않아 로그인 안되었을 경우
		} else {
			$result = array(
				'key' => 'loginFail',
				'data' => array()
			);
			echo json_encode($result);
		}
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
