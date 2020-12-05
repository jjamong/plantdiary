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
     * @brief   join : 회원가입
     */
    public function join() {
		$user_id = $this->input->post('user_id',TRUE);
		$user_password = $this->input->post('user_password',TRUE);
 
		$where = array(
			'user_id'	=>	$user_id,
			'del_yn'	=>	'N',
			'withdrawal_yn'	=>	'N',
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

	/**
     * @brief   duplicationCheck : 아이디 중복확인
     */
    public function duplicationCheck() {

		$user_id = $this->input->post('user_id', TRUE);

		$where = array(
			'user_id'	=>	$user_id,
			'del_yn'	=>	'N',
			'withdrawal_yn'	=>	'N',
		);
		$this->db->where($where);
		$query = $this->db->get('user');
		$user_count = $query->num_rows();
		
		// 응답 값 설정
		$result = array(
			'key' => '',
			'data' => array()
		);
		if ($user_count > 0) {
			$result['key'] = 'success';
		} else {
			$result['key'] = 'failure';
		}
		echo json_encode($result);
	}

	/**
     * @brief   mailSend : 메일발송
     */
    public function mailSend() {
		$user_id = $this->input->post('user_id', TRUE);

		$where = array(
			'user_id'	=>	$user_id,
			'del_yn'	=>	'N',
			'withdrawal_yn'	=>	'N',
		);
		$this->db->where($where);
		$query = $this->db->get('user');
		$user_row = $query->row();
		$user_count = $query->num_rows();

		if ($user_count > 0) {

			// 임시 비밀번호 생성
			$array_alpha = Array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
			$array_number= Array('1','2','3','4','5','6','7','8','9','0');
			$new_password = '';
			
			for ( $i=0; $i<6; $i++) { 
				$new_password .= $array_alpha[rand(0,26)];
			}  
			for ( $i=0; $i<3; $i++) { 
				$new_password .= $array_number[rand(0,9)];
			}         

			// 응답 값 설정
			$result = array(
				'key' => '',
				'data' => array()
			);


				// 이메일 발송  
				$config['mailtype'] = 'html';
				$mail_subject	= '[식물일기] 임시 비밀번호 보내드립니다';
				$message = '';
				$message .= '안녕하세요.';
				$message .= '<br>';
				$message .= '임시 비밀번호를 보내드립니다.';
				$message .= '<br>';
				$message .= $new_password;
				$message .= '<br>';
				$message .= '감사합니다.';
				//$sendMailForm		= $this->load->view('mypage/email', $data, TRUE);

				// 메일 발송
				$this->load->library('email');
				$this->email->initialize($config);
				$this->email->clear();
				$this->email->from(FROM_MAIL, FROM_NAME);
				$this->email->to($user_id);
				$this->email->subject($mail_subject);	
				$this->email->message($message);
	
				if($this->email->send()){

					// DB 처리
					$this->db->trans_start();

					$this->db->set('user_password','password("'.$new_password.'")', FALSE);
					$this->db->where('user_id', $user_row->user_id);
					$this->db->update('user');
					
					$this->db->trans_complete();

					if ($this->db->trans_status() === TRUE) {
						$result['key'] = 'success';
					} else {
						$result['key'] = 'dbFailure';
					}
					
				} else {
					$result['key'] = 'mailFailure';
				}
		} else {
			// 응답 값 설정
			$result = array(
				'key' => 'userCountfailure',
				'data' => array()
			);
		}

		echo json_encode($result);
	}
}
