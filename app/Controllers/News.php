<?php

namespace App\Controllers;

use App\Models\NewsModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class News extends BaseController 
{
    public function index(){
        $model = model(NewsModel::class);
        $data= ['news_list' => $model->getNews(),
            'title'         => 'News archive',
            'titlet' => 'News list',
        ];

        return view('templates/header', $data)
            . view('news/index')
            . view('templates/footer');
    }

    public function show(?string $slug = null){
        $model = model(NewsModel::class);
        $data['news'] = $model->getNews($slug);
        if($data == null){
            throw new PageNotFoundException('Cannot Find News Item: ' . $slug);
        }
        $data['title'] = $data['news']['title'];
        return view('template/header', $data) 
            . view('news/view')
            . view('template/footer');
    }

    public function new()
    {
        helper('form');
        return view('templates/header', ['title' => 'Create a news item'])
            . view('news/create')
            . view('templates/footer');
    }

    public function create()
    {
        // Calling the helper function for forms;
        helper('form');
        // accessing the request object to grab the data from the post method
        $data = $this->request->getPost(['title', 'body']);
        // validate data 
        if(! $this->validateData($data, [
                        // validation parameters
            'title' => 'required|max_length[255]|min_length[3]',
            'body' => 'required|max_length[5000]|min_length[10]',
        ])){
            // what to do if validation fails
            return $this->new();
        }

        // get validated data once it is validated
        $post = $this->validator->getValidated();

        // Call the model and save the data
        $model = model(NewsModel::class);
        $model->save( [
            'title' => $post['title'],
            'slug' => url_title($post['title'], '-', true),
            'body' => $post['body'],
        ]);

        // return sucess page
        return view('templates/header', ['title' => 'Create a news item'])
            . view('news/success')
            . view('templates/footer');
    }
}