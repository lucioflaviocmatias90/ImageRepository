<?php

namespace App\Http\Controllers;

use App\Post;
use App\Repositories\ImageRepository;
use Illuminate\Http\Request;

class PostController extends Controller
{
    private $repo;

    public function __construct()
    {
        $this->repo = new ImageRepository('image', 'posts');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json([
            'data' => Post::all(),
            'message' => 'Encontrado com sucesso'
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = Post::create($request->except('image'));
        $data->update(['image'=>$this->repo->uploadImage($request)]);
        return response()->json([
            'data' => $data,
            'message' => 'Criado com sucesso'
        ], 201);
//        return $this->repo->uploadImage($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        return response()->json([
            'data' => $post,
            'message' => 'Encontrado com sucesso'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        $this->repo->verifyImageInStorage($post->image);
        $request->image = $this->repo->uploadImage($request);
        $post->update($request->all());
        return response()->json([
            'data' => $post,
            'message' => 'Atualizado com sucesso'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $this->repo->verifyImageInStorage($post->image);
        $post->delete();
        return response()->json([
            'message' => 'Apagado com sucesso'
        ], 200);
    }
}
