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
}
