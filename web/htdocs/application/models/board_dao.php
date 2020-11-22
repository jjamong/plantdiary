<?php
/**
 * @Class			Board_dao
 * @Date			2014. 08. 08
 * @Author			비스톤스
 * @Brief			게시판 DAO
 */
class Board_dao extends CI_Model {

    /**
     * @brief   __construct : 생성자
     */
    function __construct() {
        parent::__construct();
    }

	/**
     * @brief		selectCount : 리스트 카운트
     * @param		String	$table_name : 테이블 명
     *					String	$where			: WHERE 배열
     *					String	$like				: LIKE 배열
     */
    function selectCount($table_name, $where="", $like="") {
		if($where != "") {
            foreach($where as $key => $val){
                if($val != "") {
                    $this->db->where($key, $val);
                }
            }
        }
		if($like != "") {
			$count = 1;
            foreach($like as $key => $val){
				if($val != "") {
					$where_text = "";
					$where_text .= $key." LIKE '%".$val."%'";
					
					if(count($like) == "1") {
						$this->db->where($where_text);
					} else {
						if($count == "1") {
							$this->db->where($where_text);
						} else if($count == count($like)) {
							$this->db->or_where($where_text);
						} else {
							$this->db->or_where($where_text);
						}
					}
					$count++;
                }
            }
        }
        return $this->db->count_all_results($table_name);
    }

	/**
     * @brief		selectList : 리스트
     * @param    String	$field				: 컬럼 명
     *					String	$table_name		: 테이블 명
     *					String	$where				: WHERE 배열
     *					String	$like					: LIKE 배열
     *					String	$where_name	: WHERE 컬럼명
     *					int			$limit				: 페이지 크기
     *					int			$offset				: 페이지 번호
     */


	function selectList($field="*", $table_name, $where="", $like="", $order, $limit, $offset) {
        $this->db->select($field);
		if($where != "") {
            foreach($where as $key => $val){
                if($val != "") {
                    $this->db->where($key, $val);
                }
            }
        }
		if($like != "") {
			$count = 1;
            foreach($like as $key => $val){
				if($val != "") {
					$where_text = "";
					$where_text .= $key." LIKE '%".$val."%'";
				
					if(count($like) == "1") {
						$this->db->where($where_text);
					} else {
						if($count == "1") {
							$this->db->where($where_text);
						} else if($count == count($like)) {
							$this->db->or_where($where_text);
						} else {
							$this->db->or_where($where_text);
						}
					}
					$count++;
                }
            }
        }
        $this->db->order_by($order);
        $query = $this->db->get($table_name, $limit, $offset);
		return $query->result();
	}
}
?>