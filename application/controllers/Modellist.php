<?php

class Modellist extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('Load_model', 'loader');
		$this->loader->loadModels();
    }

	public function saveModellist()
    {
        if($this->app_users->authenticate())
        {
            $postData = $this->input->post();
            $id = isset($postData['id']) ? $postData['id'] : null;
            $modelset_id = $this->modellist->save($postData, $id);
            $this->loader->sendresponse($modelset_id);
        }
        else
        {
            $this->loader->sendresponse();
        }
    }

    public function deleteModellist()
    {
        if($this->app_users->authenticate())
        {
            $postData = json_decode(file_get_contents('php://input'), true);
            $id = isset($postData['id']) ? $postData['id'] : null;
            $this->modellist->delete($id);
            $this->loader->sendresponse($id);
        }
        else
        {
            $this->loader->sendresponse();
        }

    }

    public function getModellist()
    {
        $modellist_id = $this->modellist->get();

        foreach($modellist_id as $m)
        {
            // $m->imagePreview = $m->image;
            // $m->icon = $this->db->query("select name from maticons where ligature in($m->icon_id)")->row()->name;
        }

        $this->loader->sendresponse($modellist_id);
    }

    public function getModellistForUser()
    {
        $modellist_id = $this->modellist->get();

        foreach($modellist_id as $m)
        {
            $m->label = $m->title;
            $m->icon = $m->icon_id;
            $m->router = $m->link;
            // $m->image = '/Applications/XAMPP/xamppfiles/htdocs/Fuse-BE/uploads/images/1.png';
        }

        $this->loader->sendresponse($modellist_id);
    }

    public function getModellistbyId($id)
    {
        $modellist_id = $this->modellist->get($id);
        // $modellist_id->image = '/Applications/XAMPP/xamppfiles/htdocs/Fuse-BE/uploads/images/1.png';
        $this->loader->sendresponse($modellist_id);
    }
}



    // public function ImageUpload()
    // {
    //     // $postData = json_decode(file_get_contents('php://input'), true);

    //         //upload file terminal access
    //         // 1.mkdir -p uploads
    //         // 2.chmod -R 777 uploads

    //         if(!is_dir('./uploads/images/'))
    //         {
    //             mkdir('./uploads/images/', 0777, TRUE);
    //         }

    //         if (!empty($_FILES['image']['name'])) 
    //         {
    //             $config['upload_path'] = './uploads/images';
    //             $config['allowed_types'] = 'jpg|jpeg|png|gif|pdf|doc|docx';
    //             $this->load->library('upload', $config);

    //             $this->upload->initialize($config);
    //             if ($this->upload->do_upload('image')) {
    //                 // File upload success
    //                 $fileData = $this->upload->data();
    //                 $response = [
    //                     'status' => 'success',
    //                     'file_name' => $fileData['file_name'],
    //                     'file_path' => base_url('uploads/images/' . $fileData['file_name']),
    //                 ];
    //                 // Process file data or save to the database
    //             } else {
    //                 // File upload failure
    //                 $error = $this->upload->display_errors();
    //                 // echo json_encode(['status' => 'error', 'message' => $error]);
    //             }
    //         }
    //         else {
    //         echo json_encode(['status' => 'error', 'message' => 'No file uploaded']);
    //         }
            
    //         $postData['image'] = base_url('uploads/images/' . $fileData['file_name']);
    //         // $postData['img'] = $fileData;
    // }