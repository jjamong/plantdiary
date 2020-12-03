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
     * @brief   myplantAllList : 내식물 전체 리스트
     */
	public function myplantAllList() {
        $user_seq = $this->input->get('user_seq', TRUE);
		
        $this->db->select('myplant_seq, myplant_name, first_grow_date, water_interval, water_day');
		$this->db->where('del_yn', 'N');
		$this->db->where('user_seq', $user_seq);
		$calendar_query = $this->db->get('myplant');
        $calendar_result = $calendar_query->result();

        // 응답 값 설정
		$result = array(
			'key' => 'success',
			'data' => array(
                'calendarResult' => $calendar_result
            )
		);
		echo json_encode($result);
    }
    
    /**
     * @brief   myplantDayList : 일자별 내식물 리스트
     */
	public function myplantDayList() {
        $user_seq = $this->input->get('user_seq', TRUE);
        $date = $this->input->get('date', TRUE);
        
		$sql = "select 
                *
            from  
                (select  
                    * 
                    from myplant 
                    where del_yn = 'N' 
                    and user_seq = $user_seq 
                    and water_day = $date 
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
        $calendar_query = $this->db->query($sql);
        $calendar_result = $calendar_query->result();
        
        // 응답 값 설정
		$result = array(
			'key' => 'success',
			'data' => array(
                'calendarResult' => $calendar_result
            )
		);
		echo json_encode($result);
    }
    
    
    /**
     * @brief   myplantWatering : 내식물 물주기
     */
	public function myplantWatering() {
        $user_seq = $this->input->get('user_seq', TRUE);
        $myplant_seq = $this->input->get('myplant_seq', TRUE);
        $state = $this->input->get('state', TRUE);

		$this->db->where('del_yn', 'N');
		$this->db->where('myplant_seq', $myplant_seq);
        $query = $this->db->get('myplant');
        $row = $query->row();

		// 물주는 날에 물을 줄 경우
        if ($state == 'waterday') {
            $update_array = array(
                'last_watering_date' => date('Ymd'),
                // 현재 날짜에서 물주기 간격만큼 더해 물줄날 설정
                'water_day' => date('Ymd', strtotime(date('Ymd').' +' . $row->water_interval . ' days')),
            );

        // 물주는 날 전에 미리 물을 줄 경우    
        } else if ($state == 'inadvance') {

            $to_day = new DateTime(date('Ymd'));
            $water_day = new DateTime($row->water_day);
            $day = date_diff($to_day, $water_day)->days;

            $update_array = array(
                'last_watering_date' => date('Ymd'),
                'water_interval' => ($day + $row->water_interval),

                // 현재 날짜에서 늘어난 물주기 간격만큼 더해 물줄날 설정
                'water_day' => date('Ymd', strtotime(date('Ymd').' +' . ($day + $row->water_interval) . ' days')),
            );
        
        // 물주는 날 후에 늦게 물을 줄 경우
        } else if ($state == 'warning') {
            $update_array = array(
                'last_watering_date' => date('Ymd'),

                // 현재 날짜에서 물주기 간격만큼 더해 물줄날 설정
                'water_day' => date('Ymd', strtotime(date('Ymd').' +' . $row->water_interval . ' days')),
            );

        }

        echo "last_watering_date = ".date('Ymd');
        echo "\nwater_interval = ".$day;
        echo "\nwater_day = ".date('Ymd', strtotime(date('Ymd').' +' . $day . ' days'));
        // echo $row->water_interval.'<br>';
        // echo $row->water_day.'<br>';

		$this->db->trans_start();

		$this->db->where('user_seq', $user_seq);
		$this->db->where('myplant_seq', $myplant_seq);
		$this->db->update('myplant', $update_array);
		
        $this->db->trans_complete();
        
        
		// $this->db->where('del_yn', 'N');
		// $this->db->where('myplant_seq', $myplant_seq);
        // $query = $this->db->get('myplant');
        // $row = $query->row();

        // echo $row->last_watering_date.'<br>';
        // echo $row->water_interval.'<br>';
        // echo $row->water_day.'<br>';
    }
}
