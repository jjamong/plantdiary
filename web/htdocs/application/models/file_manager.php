<?php
/**
 * @Class        File_manager
 * @Date         2014. 01. 26.
 * @Author        비스톤스
 * @Brief          파일 매니저
 */
class File_manager extends CI_Model {

    function __construct() {
        parent::__construct();
				
    }

    /**
     * @brief	upload_config : 업로드 설정
	 * @param   $path :  경로
	 * @param   $type : 허용될 마임타입
	 * @param   $size : 사이즈
     */
    function upload_config($path, $type, $size) {

        // 폴더 없을 경우 생성
        if (!(is_dir(UPLOAD_PATH.$path) > 0)) {
            mkdir(UPLOAD_PATH.$path, 0777, true);
        }

		$config['upload_path']		= $_SERVER['DOCUMENT_ROOT'].SITE_URL."uploads/".$path;		// 업로드 파일이 위치할 폴더경로
		$config['allowed_types']	= $type;												    // 업로드를 허용할 파일의 마임타입(mime types)을 설정
        $config['overwrite']		= FALSE;												    // 같은 이름의 파일이 이미 존재한다면 덮어쓸지 여부
        $config['max_size']			= $size;												    // 업로드 파일의 최대크기(KB)를 지정합니다 [2MB (2048KB)], 0으로 설정하면 크기 제한이 없음
        $config['max_width']		= '0';												        // 업로드 파일의 최대 높이(픽셀단위)를 설정합니다. 0이면 제한이 없습니다.
        $config['max_height']		= '0';											            // 파일이름의 최대길이를 지정합니다.0이면 제한이 없습니다.
        $config['max_filename']		= '0';													    // 파일이름의 최대길이를 지정합니다.0이면 제한이 없습니다.
        $config['encrypt_name']		= TRUE;												        // 파일이름은 랜덤하게 암호화된 문자열로 변합니다
        $config['remove_spaces']	= TRUE;												    // 파일명에 공백이 있을경우 밑줄(_)로 변경

		return $config;
    }

	/**
     * @brief   uploads : 업로드 설정
	 * @param   Array $arrayFile : 업로드 파일
     */
    function uploads($arrayFile, $tableName='', $seq='') {

		$img_file = "";
		$sys_name = array();
		$orig_name = array();

		for($i=0; $i<sizeof($arrayFile); $i++){
			if ($this->upload->do_upload($arrayFile[$i])) {
				//$upload_data = $this->upload->data($arrayFile[$i]);
				$upload_data = $this->upload->data();
				//$$arrayFile[$i] = element('file_name', $upload_data);	// 파일 이름 암호화 저장
				$sys_name[$i] = element('file_name', $upload_data);	// 파일 이름 암호화 저장
				$orig_name[$i] = element('orig_name', $upload_data);	// 파일 이름 원본 저장

				// 파일이 새로 들어오면 원래 있던 파일을 지우는 문단
				if($arrayFile[$i] && $seq!=''){
					$query = $this->db->select($arrayFile[$i])->where(strtolower(substr($tableName,3)).'_seq',$seq)->get($tableName);
					$count = $query->result();	
					foreach( $count as $row ){
						$before_img_mobile = $row->$arrayFile[$i];
					}
					if(isset($before_img_mobile)){
						$this->file_manager->deletefile($tableName, strtolower(substr($tableName,3)), $arrayFile[$i],  $before_img_mobile, $arrayFile[$i]);
					}
				}  

			} else {
				$sys_name[$i] = '';
				$orig_name[$i] = '';
			}
		}
		for($i=0; $i<sizeof($arrayFile); $i++) {
			if($sys_name[$i] != '') {
                $arrayFile[$i] = explode('-', $arrayFile[$i])[0];
                $this->db->set($arrayFile[$i], $sys_name[$i], TRUE);
				$this->db->set(substr($arrayFile[$i], 4), $orig_name[$i], TRUE);	// pc_img에 원본이름 저장
			}
		}
    }

    /**
     * @Method Name : deletefile
     * @Description : 파일 삭제
     */
    function deletefile($path, $deleteDBConfig) {
        
        $this->db->select($deleteDBConfig['file_column_name']); 
        $this->db->where($deleteDBConfig['seq_column_name'], $deleteDBConfig['seq']);	
        $query = $this->db->get($deleteDBConfig['table_name']);
        $count = $query->num_rows();
        
        if ($count > 0) {
            $row = $query->row_array();
            $filename = $row[$deleteDBConfig['file_column_name']];
            
            $delete_filename = UPLOAD_PATH.$path."/".$filename;
    
            if (is_file($delete_filename) ) {
                unlink($delete_filename);
    
                if ($deleteDBConfig['type'] == 'delete') {
                    $this->db->where($deleteDBConfig['seq_column_name'], $deleteDBConfig['seq']);
                    $this->db->delete($deleteDBConfig['table_name']);
                } else {
                    $set[str_replace('sys_', '', $deleteDBConfig['file_column_name'])] = '';
                    $set[$deleteDBConfig['file_column_name']] = '';
                    $this->db->where($deleteDBConfig['seq_column_name'], $deleteDBConfig['seq']);
                    $this->db->update($deleteDBConfig['table_name'], $set);
                }
            }
        }
    }

    /**
     * @Method Name : rmdirAll
     * @Description : 폴더 삭제
     */
    function rmdirAll($dir) {
        $dirs = dir($dir);
        while(false !== ($entry = $dirs->read())) {
            if(($entry != '.') && ($entry != '..')) {
                if(is_dir($dir.'/'.$entry)) {
                    $this->rmdirAll($dir.'/'.$entry);
                } else {
                    @unlink($dir.'/'.$entry);
                }
            }
        }
        $dirs->close();
        @rmdir($dir);
    }

    /**
     * @Method Name     : download
     * @Description    : 파일 다운로드
     */
    function download($path, $filename) {

        $download_filename = UPLOAD_PATH.$path."/".$filename;

        if ( is_file($download_filename) ) {

            // 파일 다운 //
            $this->load->helper('download');
            $data = file_get_contents(UPLOAD_PATH.$path."/".$filename);  //로컬 패스
            $name = $filename;
            force_download($name, $data);
        } else {
            goMsgPageBack("파일을 등록해주세요.");
        }
    }
}