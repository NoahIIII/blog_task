<?php

namespace App\Http\Controllers;

use App\Http\Traits\ResponseHandler;
use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    //---------------------------------- create new post --------------------------------------------
    public function addPost(Request $request)
    {
        // make validation rules
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|min:1|max:255',
            'content' => 'required|string|min:1|max:65000',
        ]);

        // validate received data
        if ($validator->fails()) {
            return ResponseHandler::errorResponse($validator->errors()->first(), 400);
        }

        // create new post
        $post = new Post();
        $post->title = $request->title;
        $post->content = $request->content;
        $post->user_id = session('user_id');

        // save new post
        $post->save();

        // return response with post id
        return ResponseHandler::successResponse(__('messages.added'), ['post_id' => $post->id], 201);
    }

    //---------------------------------- update post --------------------------------------------
    public function updatePost(Request $request, $postId)
    {
        // make validation rules
        $validator = Validator::make(array_merge($request->all(), ['post_id' => $postId]), [
            'title' => 'required|string|min:1|max:255',
            'content' => 'required|string|min:1|max:65000',
            'post_id' => 'required|numeric|exists:posts,id'
        ]);

        // validate received data
        if ($validator->fails()) {
            return ResponseHandler::errorResponse($validator->errors()->first(), 400);
        }

        // get the post & make sure it belongs to the current user
        $post = Post::where('user_id', session('user_id'))->find($postId);
        if (!$post) {
            return ResponseHandler::errorResponse(__('messages.not-found'), 404);
        }

        // update post
        $post->title = $request->title;
        $post->content = $request->content;

        // check if either 'title' or 'content' has changed
        if ($post->isDirty('title') || $post->isDirty('content')) {
            $post->save();
            return ResponseHandler::successResponse(__('messages.updated'), ['post_id' => $post->id, 'change' => true], 200);
        }
        return ResponseHandler::successResponse(__('messages.nothing-updated'), ['post_id' => $post->id, 'change' => false], 200);
    }

    //-------------------------------- bulk delete posts --------------------------------------
    public function bulkDeletePosts(Request $request)
    {
        // make validation rules
        $validator = Validator::make($request->all(), [
            'post_ids' => 'required|array',
            'post_ids.*' => 'required|numeric'
        ]);

        // validate received data
        if ($validator->fails()) {
            return ResponseHandler::errorResponse($validator->errors()->first(), 400);
        }

        // get posts
        $postIds = array_unique($request->post_ids);
        $posts = Post::whereIn('id', $postIds)->where('user_id', session('user_id'))->get();

        // check if all the posts exists
        if ($posts->count() != count($postIds)) {
            if ($posts->count() == 0) {
                return ResponseHandler::errorResponse(__('messages.not-found'), 404);
            }
            return ResponseHandler::errorResponse(__('messages.some-not-found'), 404);
        }

        // delete posts
        $posts->each(function ($post) {
            $post->delete();
        });

        return ResponseHandler::successResponse(__('messages.deleted'));
    }

    // ---------------------------- get all posts -----------------------------------------------
    public function getAllPosts(Request $request)
    {
        // get all posts paginated
        $posts = Post::withCount('likes')
        ->with('user')
        ->paginate($request->per_page ?? 10); // default = 10
        return ResponseHandler::successResponse(null, ['posts' => $posts]);
    }

    // -------------------- get single post -------------------------------------------------
    public function getPost($postId)
    {
        // validate the post id
        $validator = Validator::make(['post_id' => $postId], [
            'post_id' => 'required|numeric'
        ]);
        if ($validator->fails()) {
            return ResponseHandler::errorResponse($validator->errors()->first(), 400);
        }

        // get the post & its comments & its likes
        $post = Post::withCount('likes')
            ->with(['comments' => function ($query) {
                $query->withCount('likes')
                    ->with('user');
            }])->find($postId);

        // check if the post exists
        if (!$post) {
            return ResponseHandler::errorResponse(__('messages.not-found'), 404);
        }

        return ResponseHandler::successResponse(null, ['post' => $post]);
    }

    //----------------------------------- toggle like for posts --------------------------------------------
    public function toggleLikePost($postId)
    {
        // validate the id
        $validator = Validator::make(['post_id' => $postId], [
            'post_id' => 'required|numeric'
        ]);
        if ($validator->fails()) {
            return ResponseHandler::errorResponse($validator->errors()->first(), 400);
        }

        // get the post & check if the post exists
        $post = Post::find($postId);
        if (!$post) {
            return ResponseHandler::errorResponse(__('messages.not-found'), 404);
        }

        $like = Like::where('likable_id', $postId)
            ->where('likable_type', get_class($post))
            ->where('user_id', session('user_id'))
            ->first();

        if ($like) {
            // Unlike if already liked
            $like->delete();
            // set the status
            $status = 'unliked';
        } else {
            // Like if not liked
            $like = new Like();
            $like->user_id = session('user_id');
            $like->likable_id = $postId;
            $like->likable_type = get_class($post);
            $like->created_at = now();
            $like->save();
            // set the status
            $status = 'liked';
        }

        return ResponseHandler::successResponse(null, ['status' => $status, 'post_id' => $postId]);
    }

    // --------------------------------- get post likes ----------------------------------------------------
    public function getPostLikes($postId)
    {
        // validate the post id
        $validator = Validator::make(['post_id' => $postId], [
            'post_id' => 'required|numeric'
        ]);
        if ($validator->fails()) {
            return ResponseHandler::errorResponse($validator->errors()->first(), 400);
        }

        // get the post & check if the post exists
        $post = Post::find($postId);
        if (!$post) {
            return ResponseHandler::errorResponse(__('messages.not-found'), 404);
        }

        // get the post likes
        $likes = Like::where('likable_id', $postId)
            ->where('likable_type', get_class($post))
            ->with(['user:id,name']) // Specify columns for related model
            ->select('id', 'user_id')
            ->get();

        // check if the post has any likes
        if ($likes->isEmpty()) {
            return ResponseHandler::successResponse("This post has no likes yet", ['likes' => []]);
        }

        // Remove unnecessary data - Transform the response to include only user id and name
        $formattedLikes = $likes->map(function ($like) {
            return [
                'user_id' => $like->user_id,
                'user_name' => $like->user->name,
            ];
        });
        return ResponseHandler::successResponse(null, ['likes' => $formattedLikes]);
    }
}
