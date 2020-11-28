<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class diary extends CI_Controller {

    /**
     * @brief	__construct : 생성자
     */
    public function __construct() {
        parent::__construct();
        //$this->output->enable_profiler(TRUE);
	}
	
	/**
     * @brief	select : 상세
     */
	public function select() {
		$myplant_seq = $this->input->get('myplant_seq', TRUE);
		$diary_date = $this->input->get('diary_date', TRUE);

		$this->db->where('del_yn', 'N');
		$this->db->where('myplant_seq', $myplant_seq);
		$this->db->where('diary_date', $diary_date);
        $diary_query = $this->db->get('myplant_diary');

        $diary_row = $diary_query->row();
        $diary_count = $diary_query->num_rows();

        $diary_img_result = array();
        if ($diary_count > 0) {
            $this->db->where('myplant_diary_seq', $diary_row->myplant_diary_seq);
            $this->db->where('myplant_seq', $myplant_seq);
            $diary_img_query = $this->db->get('myplant_diary_img');
            $diary_img_result = $diary_img_query->result();
        }

        // 응답 값 설정
		$result = array(
			'key' => 'success',
			'data' => array(
                'diaryCount' => $diary_count,
                'diaryRow' => $diary_query->row(),
                'diaryImages' => $diary_img_result
            )
		);
		echo json_encode($result);
    }

    /**
     * @brief   insert : 등록
     */
	function insert() {
        
		// 다이어리 정보 설정
        $user_seq = $this->input->post('user_seq', TRUE);
        $myplant_seq = $this->input->post('myplant_seq', TRUE);
		$diary_date = $this->input->post('diary_date', TRUE);
		$water_yn = $this->input->post('water_yn', TRUE);
		$soil_condition_yn = $this->input->post('soil_condition_yn', TRUE);
		$medicine_yn = $this->input->post('medicine_yn', TRUE);
		$pot_replace_yn = $this->input->post('pot_replace_yn', TRUE);
        $diary_content = $this->input->post('diary_content', TRUE);
        $sys_diary_img_1 = $_FILES['sys_diary_img-1']['name'];
        $sys_diary_img_2 = $_FILES['sys_diary_img-2']['name'];
        $sys_diary_img_3 = $_FILES['sys_diary_img-3']['name'];

		$diary_set = array(
			'diary_date' => $diary_date,
			'water_yn' => $water_yn,
			'soil_condition_yn' => $soil_condition_yn,
			'medicine_yn' => $medicine_yn,
			'pot_replace_yn' => $pot_replace_yn,
			'diary_content' => $diary_content,
			'del_yn' => 'N',
			'user_seq' => $user_seq,
			'myplant_seq' => $myplant_seq,
			'reg_date' => date('Ymd'),
			'reg_time' => date('His')
        );
        
		$this->db->where('del_yn', 'N');
		$this->db->where('myplant_seq', $myplant_seq);
		$this->db->where('diary_date', $diary_date);
        $diary_query = $this->db->get('myplant_diary');
        $diary_count = $diary_query->num_rows();

        // 날짜별 다이어리 데이터가 없을 경우
        if ($diary_count == 0) {

            // DB 처리
            $this->db->trans_start();

            // 다이어리 저장
            $this->db->insert('myplant_diary', $diary_set);

            $this->db->order_by('myplant_diary_seq', 'DESC');
            $this->db->limit(1);
            $query = $this->db->get('myplant_diary');
            $diaryRow = $query->row();
            $myplant_diary_seq = $diaryRow->myplant_diary_seq;
            
            // 다이어리 이미지 저장
            $arrayFile = array();
            if ($sys_diary_img_1) array_push($arrayFile, 'sys_diary_img-1');
            if ($sys_diary_img_2) array_push($arrayFile, 'sys_diary_img-2');
            if ($sys_diary_img_3) array_push($arrayFile, 'sys_diary_img-3');

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
            } else {
                $result['key'] = 'failure';
            }

        // 날짜별 다이어리 데이터가 있을 경우
        } else {
            // 응답 값 설정
            $result = array(
                'key' => 'insertFailure',
                'data' => array(
                    'diaryCount' => $diary_count
                )
            );
        }

		echo json_encode($result);
    }
    
    /**
     * @brief   update : 수정
     */
	function update() {

		// 다이어리 정보 설정
        $user_seq = $this->input->post('user_seq', TRUE);
        $myplant_seq = $this->input->post('myplant_seq', TRUE);
        $myplant_diary_seq = $this->input->post('myplant_diary_seq', TRUE);
        
		$diary_date = $this->input->post('diary_date', TRUE);
		$water_yn = $this->input->post('water_yn', TRUE);
		$soil_condition_yn = $this->input->post('soil_condition_yn', TRUE);
		$medicine_yn = $this->input->post('medicine_yn', TRUE);
		$pot_replace_yn = $this->input->post('pot_replace_yn', TRUE);
        $diary_content = $this->input->post('diary_content', TRUE);
        
        $sys_diary_img_1 = $_FILES['sys_diary_img-1']['name'];
        $sys_diary_img_2 = $_FILES['sys_diary_img-2']['name'];
        $sys_diary_img_3 = $_FILES['sys_diary_img-3']['name'];

		$diary_set = array(
			'diary_date' => $diary_date,
			'water_yn' => $water_yn,
			'soil_condition_yn' => $soil_condition_yn,
			'medicine_yn' => $medicine_yn,
			'pot_replace_yn' => $pot_replace_yn,
			'diary_content' => $diary_content,
			'upd_date' => date('Ymd'),
			'upd_time' => date('His')
        );
        
		$diary_where = array(
			'user_seq'	=>	$user_seq,
			'myplant_seq'	=>	$myplant_seq,
            'water_yn' => 'Y',
			'del_yn'	=>	'N',
		);
		$this->db->where($diary_where);
		$query = $this->db->get('myplant_diary');
        $diary_count = $query->num_rows();
        
        // 전체 다이러리 물주기 개수가 1개일 경우에 물주기 비활성화 변경 시 
        if ($diary_count == 1 && $water_yn == 'N') {

            // 응답 값 설정
            $result = array(
                'key' => 'waterCountFailure',
                'data' => array()
            );
            echo json_encode($result);
            return;
        }

        // DB 처리
        $this->db->trans_start();

        // 다이어리 수정
		$this->db->where('myplant_diary_seq', $myplant_diary_seq);
        $this->db->update('myplant_diary', $diary_set);
        
        // 다이어리 이미지 수정
        $arrayFile = array();
        if ($sys_diary_img_1) array_push($arrayFile, 'sys_diary_img-1');
        if ($sys_diary_img_2) array_push($arrayFile, 'sys_diary_img-2');
        if ($sys_diary_img_3) array_push($arrayFile, 'sys_diary_img-3');
        
        for($i=0; $i<sizeof($arrayFile); $i++) {
            // $path = 'user_'.$user_seq.'/myplant_'.$myplant_seq.'/diary_'.$myplant_diary_seq;
            // $type = 'gif|jpg|jpeg|png';
            // $size = '0';

            // // 폴더 없을 경우 생성
            // if (!(is_dir(UPLOAD_PATH.$path) > 0)) {
            // 	mkdir(UPLOAD_PATH.$path, 0777, true);
            // }
            // // 파일을 변경 했을 경우
            // if($sys_diary_img_1 != '') {
            //     $this->file_manager->deletefile('myplant_diary_img', $path, 'myplant_diary_img_seq',  $myplant_diary_img_seq, 'sys_myplant_img');
            // }

            // $config = $this->file_manager->upload_config($path, 'gif|jpg|jpeg|png', '0');
            // $this->load->library('upload', $config);
            // $this->upload->initialize($config);

            
            // $this->file_manager->uploads(array($arrayFile[$i]), 'myplant_diary');
            // $diary_img_set = array(
            // 	'user_seq' => $user_seq,
            // 	'myplant_seq' => $myplant_seq,
            // 	'myplant_diary_seq' => $myplant_diary_seq,
            // );
            // $this->db->insert('myplant_diary_img', $diary_img_set);
        }

        $this->db->trans_complete();

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
