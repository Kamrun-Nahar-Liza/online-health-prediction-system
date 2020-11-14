<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\Category;
use Validator;
use Auth;
use App\User;

use DB;

class PostController extends Controller
{
    
    public function index()
    {
        $data=[];
         //here category in with is relation name .its eager loading from less query
        $data['posts'] = Post::with('category','user')->select('id','title','content','user_id','category_id','status')->get();

        return view('post.index', $data);
    }

    
    public function create()
    {
         $data=[];

        $data['categories'] = Category::select('name','id')->get();
        return view('post.create', $data );
    }

    
    public function store(Request $request)
    {
        //validation

        $rules= [
            'title' => 'required',
            'content' => 'required',
            'status' => 'required',
            'category_id' => 'required'
   
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        //database insert

        Post::create([

            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'status' => $request->input('status'),
            'category_id' => $request->input('category_id'),
            'user_id' => auth()->user()->id
        ]);

        //redirect

        session()->flash('message','Recovery Post Added Successfully');
        session()->flash('type','success');
        return redirect()->route('posts.create');
    }

    
    public function show($id)
    {
        $data=[];
        $data['post'] = Post::with('category','user')->select('id','title','content','status','created_at')->find($id);
       // $data['categories'] = Category::select('name','id')->find($id);


        return view('post.show', $data);
    }

    
    public function edit($id)
    {
        $data=[];
        $data['post'] = Post::with('category')->select('id','title','content','category_id','status','created_at')->find($id);
        $data['categories'] = Category::select('name','id')->get();
        return view('post.edit', $data);
    }

    
    public function update(Request $request, $id)
    {
        //validation

        $rules= [
            'title' => 'required',
            'content'=> 'required',
            'category_id'=>'required',
            'status' => 'required'
           
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        //database update

        $post = Post::find($id);

        $post->update([

            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'category_id' => $request->input('category_id'),
            'status' => $request->input('status')
        ]);

        //redirect

        session()->flash('message','post has been updated');
        session()->flash('type','success');
        return redirect()->back();
    }

    
    public function destroy($id)
    {
        $post = Post::find($id);
        $post->delete();

        //redirect

        session()->flash('message','Post deleted');
        session()->flash('type','danger');
        return redirect()->route('posts.index');
    }
}