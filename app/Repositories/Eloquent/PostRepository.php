<?php


namespace App\Repositories\Eloquent;


use App\Post;
use App\Repositories\Contracts\PostRepositoryInterface;

class PostRepository implements PostRepositoryInterface
{
    private $post;

    /**
     * PostRepository constructor.
     * @param $post
     */
    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    public function all()
    {
        return $this->post->all();
    }

    public function find(int $id)
    {
        return $this->post->findOrFail($id);
    }

    public function create(array $attributes)
    {
        return $this->post->create($attributes);
    }

    public function update(int $id, array $attributes)
    {
        $post = $this->post->findOrFail($id);
        return $post->update($attributes);
    }

    public function delete(int $id)
    {
        $this->post->findOrFail($id)->delete();
    }
}