<?php

namespace App\Http\Controllers;

use App\Post;
use App\Services\ImageService;
use Illuminate\Http\Request;

class PostController extends Controller
{
    private $service;

    public function __construct()
    {
        $this->service = new ImageService(['image', 'cover'], 'posts');
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
        $data = Post::create($request->except(['image', 'cover']));
        $this->service->uploadImage($request, $data);
        return response()->json([
            'data' => $data,
            'message' => 'Criado com sucesso'
        ], 201);
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
        $this->service->verifyImageInStorage($post);
        $post->update($request->except(['image', 'cover']));
        $this->service->uploadImage($request, $post);
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
        $this->service->verifyImageInStorage($post);
        $post->delete();
        return response()->json([
            'message' => 'Apagado com sucesso'
        ], 200);
    }
}
