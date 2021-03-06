<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Post;
use App\Models\Text;
use App\Models\Image;
use App\Models\User;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\ErrorHandler\Debug;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $admin = User::where('email', env('ADMIN_MAIL'))->first();
        $posts = $admin->posts()->orderBy('created_at', 'DESC')->get();

        return view('home', ['posts' => $posts]);
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("posts.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $title = $data['title'];

        $post = new Post();
        $post->title = $title;
        $post->user_id = Auth::id();

        $post->save();
        $post->user()->associate(auth()->user()->id);

        if($request->has('texts')){
            $texts = $data['texts'];
            foreach($texts as $text){
                $element = $text['txt'];
                $index = $text['pos'];
    
                $txt = new Text();
                $txt->content = $element;
                $txt->position = $index;
                $txt->post_id = $post->id;
    
                $txt->save();
                $txt->post()->associate($post->id);
            }
        }

        if($request->has('images')){
            $request->file('images');
            $images = $data['images'];
            foreach($images as $image){
                $path = $image['img']->store("public/img/posts");
                $path = substr($path, 6, strlen($path));
                $img = new Image();
                $img->url = $path;
                $img->position = $image['pos'];
                $img->post_id = $post->id;
                $img->save();
                $img->post()->associate($post->id);
            }
        }

        $admin = User::where('email', env('ADMIN_MAIL'))->first();
        $posts = $admin->posts()->orderBy('created_at', 'DESC')->get();

        return $posts;

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Note  $note
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = Post::find($id);

        $images = Image::where('post_id', $post->id)->get();
        $texts = Text::all()->where('post_id', $post->id);

        $txts = [];
        $imgs = [];

        foreach($texts as $text){
            array_push($txts, $text);
        }

        foreach($images as $image){
            array_push($imgs, $image);
        }

        return view('posts.show', ['post' => $post, 'images' => $imgs, 'texts' => $txts]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Note  $note
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $post = Post::find($id);
        $images = Image::where('post_id', $post->id)->get();
        $texts = Text::all()->where('post_id', $post->id);

        $txts = [];
        $imgs = [];

        foreach($texts as $text){
            array_push($txts, $text);
        }

        foreach($images as $image){
            array_push($imgs, $image);
        }

        return view("posts.edit", ['post' => $post, 'images' => $imgs, 'texts' => $txts]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Note  $note
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();
        
        $title = $data['title'];

        $post = Post::find($id);

        $post->update(['title' => $title]);

        if($request->has('texts')){
            $texts = $data['texts'];
            
            $former_texts = Text::where("post_id", $id)->get();
            if($request->has('deleted_texts')){
                $deleted_txts = $data['deleted_txts'];
                foreach($deleted_txts as $deleted_txt){
                    $txt = Text::where('post_id', $id, 'and')->where('position', $deleted_txt['id'])->first();
                    $txt->delete();
                }
            }
            
            $texts_without_deleted = Text::where("post_id", $id)->get();
            $index=0;
            foreach($former_texts as $former_text){
                if(array_key_exists($index, $texts)){
                    $text = $texts[$index];
                    $element = $text['txt'];
                    $position = $text['pos'];
                    
                    $former_text->update(['content' => $element]);
                    $former_text->update(['position' => $position]);
                }else{
                    $former_text->delete();
                }
                $index++;
            }

            $i = 0;
            if($texts_without_deleted){
                $i = count($texts_without_deleted);
            }

            while($i < count($texts)){
                $text = $texts[$i];
                $element = $text['txt'];
                $position = $text['pos'];
    
                $txt = new Text();
                $txt->content = $element;
                $txt->position = $position;
                $txt->post_id = $post->id;
    
                $txt->save();
                $txt->post()->associate($post->id);
                $i++;
            }
        }

        if($request->has('deleted_imgs')){
            $deleted_imgs = $data['deleted_imgs'];
            foreach($deleted_imgs as $image){
                $img = Image::where('url', $image['img']);
                $img->delete();
                unlink('storage/'.$image['img']);
            }
        }

        if($request->has('updated_imgs')){
            $request->file('updated_imgs');
            $updated_imgs = $data['updated_imgs'];
            foreach($updated_imgs as $image){
                $img = Image::where('url', $image['img']);
                $img->delete();
                unlink('storage/'.$image['img']);
                $path = $image['file']->store("public/img/posts");
                $path = substr($path, 6, strlen($path));
                $img = new Image();
                $img->url = $path;
                $img->position = $image['pos'];
                $img->post_id = $post->id;
                $img->save();
                $img->post()->associate($post->id);
            }
        }

        if($request->has('images')){
            $request->file('images');
            $images = $data['images'];
            foreach($images as $image){
                if(gettype($image['img'])=='object'){
                    $path = $image['img']->store("public/img/posts");
                    $path = substr($path, 6, strlen($path));
                    $img = new Image();
                    $img->url = $path;
                    $img->position = $image['pos'];
                    $img->post_id = $post->id;
                    $img->save();
                    $img->post()->associate($post->id);
                }else{
                    $img = Image::where('url', $image['img']);
                    $img->update(['position' => $image['pos']]);
                }
                
            }
        }

        $admin = User::where('email', env('ADMIN_MAIL'))->first();
        $posts = $admin->posts()->orderBy('created_at', 'DESC')->get();

        return $posts;

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Note  $note
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::find($id);
        $images = Image::where('post_id', $post->id)->get();
        foreach($images as $image){
            unlink('storage/'.$image->url);
        }
        $post->delete();

        /*$admin = User::where('email', env('ADMIN_MAIL'))->first();
        $posts = $admin->posts()->orderBy('created_at', 'DESC')->get();

        return view('home', ['posts' => $posts]);*/

        return redirect()->back();
    }
}
