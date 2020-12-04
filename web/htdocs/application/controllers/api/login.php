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
			'withdrawal_yn'	=>	'N',
		);
		$this->db->where($where);
		$this->db->where('user_password','password("'.$user_password.'")', FALSE);
		$user_query = $this->db->get('user');
		$user_row = $user_query->row();
		$login_count = $user_query->num_rows();

		// 아이디 비밀번호가 맞아 로그인 되었을 경우
		if ($login_count > 0 ) {

            $this->db->where('del_yn', 'N');
			$this->db->where('user_seq', $user_row->user_seq);
            $myplant_query = $this->db->get('myplant');
			$myplant_result = $myplant_query->result();
			
            $notificationData = array();
			foreach ($myplant_result as $val) {
				$array = array(
					'myplantSeq' => $val->myplant_seq,
					'myplantName' => $val->myplant_name,
					'waterDay' => substr($val->water_day, 0, 4).'-'.substr($val->water_day, 4, 2).'-'.substr($val->water_day, 6, 2),
				);
				array_push($notificationData, $array);
			}
			
            // 응답 값 설정
            $result = array(
                'key' => 'loginSuccess',
                'data' => array(
					'userRow' => $user_row,
                    'notificationData' => $notificationData
				)
            );
			
		// 아이디 비밀번호가 맞지않아 로그인 안되었을 경우
		} else {
            // 응답 값 설정
            $result = array(
                'key' => 'loginFailure',
                'data' => array()
            );
		}

		echo json_encode($result);
	}

	/**
     * @brief   passwordCheck : 비밀번호 확인
     */
    public function passwordCheck() {
		$user_seq = $this->input->post('user_seq', TRUE);
		$user_password = $this->input->post('user_password', TRUE);
 
		$where = array(
			'user_seq'	=>	$user_seq,
			'withdrawal_yn'	=>	'N',
			'del_yn'	=>	'N',
		);
		$this->db->where($where);
		$this->db->where('user_password','password("'.$user_password.'")', FALSE);
		$query = $this->db->get('user');
		$userCount = $query->num_rows();

		$result = array(
			'key' => '',
			'data' => array()
		);
		if ($userCount > 0) {
			$result['key'] = 'success';
		} else {
			$result['key'] = 'failure';
		}

		echo json_encode($result);
	}

	/**
     * @brief   passwordChange : 비밀번호 변경
     */
    public function passwordChange() {
		$user_seq = $this->input->post('user_seq', TRUE);
		$user_password = $this->input->post('user_password', TRUE);
 
		$where = array(
			'user_seq'	=>	$user_seq,
			'withdrawal_yn'	=>	'N',
			'del_yn'	=>	'N',
		);
		$this->db->where($where);
		$user_query = $this->db->get('user');
		$user_count = $user_query->num_rows();

		if ($user_count > 0) {
			// DB 처리
			$this->db->trans_start();

			$this->db->set('user_password', 'password("'.$user_password.'")', FALSE );
			$this->db->where($where);
			$this->db->update('user');

			$this->db->trans_complete();
		}

		// 응답 값 설정
		$result = array(
			'key' => '',
			'data' => array()
		);
		if ($this->db->trans_status() === TRUE) {
			$result['key'] = 'success';
		} else {
			$result['key'] = 'failure';
		}
		echo json_encode($result);
	}

	/**
     * @brief   withdrawal : 회원탈퇴
     */
    public function withdrawal() {
		$user_seq = $this->input->post('user_seq', TRUE);
 
		$where = array(
			'user_seq'	=>	$user_seq,
			'withdrawal_yn'	=>	'N',
			'del_yn'	=>	'N',
		);
		$this->db->where($where);
		$user_query = $this->db->get('user');
		$user_count = $user_query->num_rows();

		if ($user_count > 0) {
			// DB 처리
			$this->db->trans_start();
	
			$this->db->set('withdrawal_yn', 'Y');
			$this->db->where($where);
			$this->db->update('user');
			
			$this->db->trans_complete();
		}

		// 응답 값 설정
		$result = array(
			'key' => '',
			'data' => array()
		);
		if ($this->db->trans_status() === TRUE) {
			$result['key'] = 'success';
		} else {
			$result['key'] = 'failure';
		}
		echo json_encode($result);
	}
}
