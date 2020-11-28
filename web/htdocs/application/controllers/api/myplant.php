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
     * @brief	index : 리스트
     */
	public function list()	{
		$user_seq = $this->input->get('user_seq', TRUE);
		$page = $this->input->get('page', TRUE);
		//$page = ($page) ? $page : 30;

		$sql = "select 
					*
				from  
					(select  
						* 
						from myplant 
						where del_yn = 'N' 
						and user_seq = $user_seq 
					) as myplant  
					inner join 
					(select 
						myplant_diary_seq, 
						myplant_seq, 
						diary_date 
					from myplant_diary_recent_diary_date
					) as myplant_diary 
				on myplant.myplant_seq  = myplant_diary.myplant_seq
				order by myplant.myplant_seq desc";
		$query = $this->db->query($sql);

        // 응답 값 설정
		$result = array(
			'key' => 'success',
			'data' => $query->result()
		);
		echo json_encode($result);
	}
	
	/**
     * @brief	select : 상세
     */
	public function select() {
		
		// 내식물
		$user_seq = $this->input->get('user_seq', TRUE);
		$myplant_seq = $this->input->get('myplant_seq', TRUE);

		$sql = "select 
					*
				from  
					(select  
						* 
						from myplant 
						where del_yn = 'N' 
						and user_seq = $user_seq 
						and myplant_seq = $myplant_seq 
					) as myplant  
					inner join 
					(select 
						myplant_diary_seq, 
						myplant_seq, 
						diary_date 
					from myplant_diary_recent_diary_date
					) as myplant_diary 
				on myplant.myplant_seq  = myplant_diary.myplant_seq";
		$myplant_query = $this->db->query($sql);

		// 내식물 아래 최근 3개 다이어리
		$this->db->where('del_yn', 'N');
		$this->db->where('myplant_seq', $myplant_seq);
		$this->db->order_by('myplant_diary_seq', 'DESC');
		$this->db->limit(3);
        $diary_query = $this->db->get('myplant_diary');

        // 응답 값 설정
		$result = array(
			'key' => 'success',
			'data' => array(
				'mplantRow' => $myplant_query->row(),
				'diaryResult' => $diary_query->result()
            )
		);
		echo json_encode($result);
	}

	/**
     * @brief   insert : 등록
     */
	function insert() {
		
		// 내식물 정보 설정
		$sys_myplant_img = $_FILES['sys_myplant_img']['name'];
		$myplant_name = $this->input->post('myplant_name', TRUE);
		$first_grow_date = $this->input->post('first_grow_date', TRUE);
		$water_interval = $this->input->post('water_interval', TRUE);
		$water_day = $this->input->post('water_day', TRUE);
		$user_seq = $this->input->post('user_seq', TRUE);

		$myplant_set = array(
			'myplant_name' => $myplant_name,
			'first_grow_date' => $first_grow_date,
			'water_interval' => $water_interval,
			'water_day' => $water_day,
			'del_yn' => 'N',
			'user_seq' => $user_seq,
			'reg_date' => date('Ymd'),
			'reg_time' => date('His')
		);

		// 다이어리 정보 설정
		$diary_date = $this->input->post('diary_date', TRUE);
		$water_yn = $this->input->post('water_yn', TRUE);
		$soil_condition_yn = $this->input->post('soil_condition_yn', TRUE);
		$medicine_yn = $this->input->post('medicine_yn', TRUE);
		$pot_replace_yn = $this->input->post('pot_replace_yn', TRUE);
		$diary_content = $this->input->post('diary_content', TRUE);

		$diary_set = array(
			'diary_date' => $diary_date,
			'water_yn' => $water_yn,
			'soil_condition_yn' => $soil_condition_yn,
			'medicine_yn' => $medicine_yn,
			'pot_replace_yn' => $pot_replace_yn,
			'diary_content' => $diary_content,
			'del_yn' => 'N',
			'user_seq' => $user_seq,
			'reg_date' => date('Ymd'),
			'reg_time' => date('His')
		);

		// DB 처리
		$this->db->trans_start();

		// 내식물 저장
		$this->db->insert('myplant', $myplant_set);

		$this->db->order_by('myplant_seq', 'DESC');
		$this->db->limit(1);
		$query = $this->db->get('myplant');
		$row = $query->row();
		$myplant_seq = $row->myplant_seq;

		// 내식물 이미지 저장
		$path = 'user_'.$user_seq.'/myplant_'.$myplant_seq;
		$this->load->library('upload', $this->file_manager->upload_config($path, 'gif|jpg|jpeg|png', '0')); //1024kb = 1mb , 10240kb=10mb
		if($sys_myplant_img != '') {
			$arrayFile = array('sys_myplant_img'); 
			$this->file_manager->uploads($arrayFile, 'myplant');
			
			$this->db->where('myplant_seq', $myplant_seq);
			$this->db->update('myplant');
		}

		// 다이어리 저장
		$this->db->set('myplant_seq', $row->myplant_seq);
		$this->db->insert('myplant_diary', $diary_set);

		$this->db->order_by('myplant_diary_seq', 'DESC');
		$this->db->limit(1);
		$query = $this->db->get('myplant_diary');
		$diaryRow = $query->row();
		$myplant_diary_seq = $diaryRow->myplant_diary_seq;
		
		// 다이어리 이미지 저장
		$arrayFile = array('sys_diary_img-1', 'sys_diary_img-2', 'sys_diary_img-3');
		for($i=0; $i<sizeof($arrayFile); $i++) {
			
			$path = 'user_'.$user_seq.'/myplant_'.$myplant_seq.'/diary_'.$myplant_diary_seq;
			$type = 'gif|jpg|jpeg|png';
			$size = '0';

			// 폴더 없을 경우 생성
			if (!(is_dir(UPLOAD_PATH.$path) > 0)) {
				mkdir(UPLOAD_PATH.$path, 0777, true);
			}
	
			$config = $this->file_manager->upload_config($path, 'gif|jpg|jpeg|png', '0');
			$this->load->library('upload', $config);
			$this->upload->initialize($config);
			
			$this->file_manager->uploads(array($arrayFile[$i]), 'myplant_diary');
			$diary_img_set = array(
				'user_seq' => $user_seq,
				'myplant_seq' => $myplant_seq,
				'myplant_diary_seq' => $myplant_diary_seq,
			);
			$this->db->insert('myplant_diary_img', $diary_img_set);
		}

		$this->db->trans_complete();

		// 응답 값 설정
		$result = array(
			'key' => '',
			'data' => array()
		);
		if ($this->db->trans_status() === TRUE) {
			$result['key'] = 'success';
			$result['data'] = array(
				'myplantSeq' => $myplant_seq
			);
		} else {
			$result['key'] = 'failure';
		}
		echo json_encode($result);
	}

	/**
     * @brief   update : 수정
     */
	function update() {
		
		$user_seq = $this->input->post('user_seq', TRUE);
		$myplant_seq = $this->input->post('myplant_seq', TRUE);
		$myplant_name = $this->input->post('myplant_name', TRUE);
		$sys_myplant_img = $_FILES['sys_myplant_img']['name'];
		$adop_date = $this->input->post('adop_date', TRUE);
		$last_watering_date = $this->input->post('last_watering_date', TRUE);
		$water_interval = $this->input->post('water_interval', TRUE);

		$update_array = array(
			'myplant_name' => $myplant_name,
			'adop_date' => $adop_date,
			'last_watering_date' => $last_watering_date,
			'water_interval' => $water_interval,
			'upd_date'	=>date('Ymd'),
			'upd_time'	=>date('His')
		);

		$this->db->trans_start();

		// 파일을 변경 했을 경우
		if($sys_myplant_img != '') {
			$this->file_manager->deletefile('myplant', 'myplant/'.$user_seq, 'myplant_seq',  $myplant_seq, 'sys_myplant_img');
		}

		$this->load->library('upload', $this->file_manager->upload_config('myplant/'.$user_seq, 'gif|jpg|jpeg|png', '0')); //1024kb = 1mb , 10240kb=10mb
		$arrayFile = array('sys_myplant_img'); 
		$this->file_manager->uploads($arrayFile , 'myplant');
		

		$this->db->where('myplant_seq', $myplant_seq);
		$this->db->update('myplant', $update_array);
		
		$this->db->trans_complete();
	}

	/**
     * @brief delete : 삭제
     */
	function delete() {
		$user_seq = $this->input->post('user_seq', TRUE);
		$myplant_seq = $this->input->post('myplant_seq', TRUE);

		$this->db->where('del_yn', 'N');
		$this->db->where('myplant_seq', $myplant_seq);
		$myplant_query = $this->db->get('myplant');
        $myplant_row = $myplant_query->row();
		$myplant_count = $myplant_query->num_rows();

		if ($myplant_count > 0) {

			$this->db->trans_start();

			// 폴더 하위로 전부
			$path = 'user_'.$user_seq.'/myplant_'.$myplant_seq;
			if ((is_dir(UPLOAD_PATH.$path) > 0)) {
				rmdir(UPLOAD_PATH.$path);
			}

			$this->db->set('del_yn', 'Y');
			$this->db->where('myplant_seq', $myplant_seq);
			$this->db->update('myplant');

			$this->db->set('del_yn', 'Y');
			$this->db->where('myplant_seq', $myplant_seq);
			$this->db->update('myplant_diary');
			
			$this->db->trans_complete();

			// 응답 값 설정
			$result = array(
				'key' => '',
				'data' => array()
			);
			if ($this->db->trans_status() === TRUE) {
				$result['key'] = 'success';
				$result['data'] = array();
			} else {
				$result['key'] = 'failure';
			}
			
		} else {
			$result = array(
				'key' => 'countFailure',
				'data' => array()
			);
		}
		echo json_encode($result);
	}
}