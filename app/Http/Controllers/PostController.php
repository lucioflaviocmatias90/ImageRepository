<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Http\Resources\PostCollection;
use App\Http\Resources\Posts as PostResource;
use App\Repositories\Contracts\PostRepositoryInterface;
use App\Repositories\Eloquent\PostRepository;
use App\Services\ImageService;

class PostController extends Controller
{
    private $post;
    private $service;

    public function __construct(PostRepositoryInterface $post)
    {
        $this->post = $post;
        $this->service = new ImageService(['image', 'cover'], 'posts');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return (new PostResource($this->post->all()));
//        return (new PostCollection($this->post->all()));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostRequest $request)
    {
        $post = $this->post->create($request->except(['image', 'cover']));
        return (new PostResource($post))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = $this->post->find($id);
        return (new PostResource($post))->response()->setStatusCode(200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(PostRequest $request, $id)
    {
        $post = $this->post->update($id, $request->except(['image', 'cover']));
        return (new PostResource($post))->response()->setStatusCode(200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // $this->service->verifyImageInStorage($post);
        $$this->post->delete($id);
        return response()->json([
            'message' => 'Apagado com sucesso'
        ], 200);
    }
}
