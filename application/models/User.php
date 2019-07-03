<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Model {

	public function to_edit_user($id_user)
	{
		return $this->db->select('*')
						->where('id_user',$id_user)
						->get('tb_user')
						->row();
	}

	public function edit_user()
	{
		$nama = $this->input->post('nama_user');
		$username = $this->input->post('username');
		$password = $this->input->post('password');
		$confirm_password = $this->input->post('confirm_password');
		$email = $this->input->post('email');
		$level = $this->input->post('level');
		$id_user = $this->input->post('id_user');

		if($password != NULL && $confirm_password != NULL){

			$data = array(
				'nama' => $nama,
				'username'  => $username,
				'email' => $email,
			);

			$this->db->where('id_user',$id_user)
				 	->update('tb_user',$data);

			$this->db->update('tb_user',array('password' => md5($password),'confirm_password' => md5($confirm_password)),array('id_user' => $id_user));

		}else{

			$data = array(
				'nama' => $nama,
				'username' => $username,
				'email' => $email,
			);

			$this->db->where('id_user',$id_user)
					 ->update('tb_user',$data);
		}

		if($this->session->userdata('level') == 'admin'){
			$this->db->update('tb_user',array('level' => $level),array('id_user' => $id_user));
		}
		
		return "here";
		
		if($this->db->affected_rows() > 0){
			return TRUE;
		}else{
			return FALSE;
		}
	}

	public function change_password()
	{
		$id_user = $this->session->userdata('id_user');
		$password = $this->input->post('password');
		$confirm_password = $this->input->post('confirm_password');

		if($password == $confirm_password){
			
			$data = array(
			'password' => md5($password),
			'confirm_password' => md5($confirm_password) 
			);

			$this->db->where('id_user',$this->session->userdata('id_user'))
					->update('tb_user',$data);

			return TRUE;
		}else{
			return FALSE;
		}
	}

	public function login()
	{
		$username = $this->input->post('username');
		$password = $this->input->post('password');

		$query = $this->db->where('username',$username)
				 ->where('password',md5($password))
				 ->where('deleted','0')
				 ->get('tb_user');
		if($query->num_rows() == 1){
			$data = array(
				'username' => $username,
				'logged_in'=> TRUE,
				'id_user' => $query->row()->id_user,
				'nama_user' => $query->row()->nama,
				'level' => $query->row()->level 
			);
			
			$this->session->set_userdata($data);
			return TRUE;

		}else{
			return FALSE;
		}
	}

}

/* End of file User.php */
/* Location: ./application/models/User.php */