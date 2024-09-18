<?php

namespace App\Http\Controllers;

use App\Http\Traits\ResponseHandler;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Notifications\NotificationSender;


class CommentController extends Controller
{
    //-------------------------------- add comment -----------------------------------------

    public function addComment(Request $request, $postId)
    {
        // make validation rules
        $validator = Validator::make(array_merge($request->all(), ['post_id' => $postId]), [
            'content' => 'required|string|min:1|max:255',
            'post_id' => 'required|numeric|exists:posts,id'
        ]);

        // validate received data
        if ($validator->fails()) {
            return ResponseHandler::errorResponse($validator->errors()->first(), 400);
        }

        // create new comment
        $comment = new Comment();
        $comment->content = $request->content;
        $comment->post_id = $postId;
        $comment->user_id = session('user_id');

        // save new comment
        $comment->save();

        // get the user that created the post
        $post = Post::find($postId);
        $postCreator = $post->user;

        // check if the user commented to his self don't send email notifications

        if (session('user_id') != $postCreator->id) {
            // initialize notification data in $data
            $data['post_title'] = $post->title;
            $data['comment_author'] = $comment->user->name;
            $data['comment_content'] = $comment->content;
            $data['comment_time'] = $comment->created_at;
            $data['post_author'] = $post->user->name;
            app()->setlocale($postCreator->locale);

            // send notification to inform user there's new comment on his post
            try {
                $notificationData = NotificationService::getNotificationData('new-comment', $data);
                $postCreator->notify(new NotificationSender($notificationData));
            } catch (\Throwable $th) {
                // do nothing
            }
        }

        // return response with comment id
        return ResponseHandler::successResponse(__('messages.added'), ['comment_id' => $comment->id], 201);
    }

    //-------------------------------- delete comment -----------------------------------------

    public function deleteComment($commentId)
    {
        // validate the comment id
        $validator = Validator::make(['comment_id' => $commentId], [
            'comment_id' => 'required|numeric'
        ]);

        // validate received data
        if ($validator->fails()) {
            return ResponseHandler::errorResponse($validator->errors()->first(), 400);
        }

        // get the comment & make sure it belongs to the current user
        $comment = Comment::where('user_id', session('user_id'))->find($commentId);
        if (!$comment) {
            return ResponseHandler::errorResponse(__('messages.not-found'), 404);
        }

        // delete comment
        $comment->delete();

        // return response
        return ResponseHandler::successResponse(__('messages.deleted'));
    }

    //----------------------------------- toggle like for comments --------------------------------------------
    public function toggleLikeComment($commentId)
    {
        // validate the id
        $validator = Validator::make(['comment_id' => $commentId], [
            'comment_id' => 'required|numeric'
        ]);
        if ($validator->fails()) {
            return ResponseHandler::errorResponse($validator->errors()->first(), 400);
        }

        // get the comment & check if the comment exists
        $comment = Comment::find($commentId);
        if (!$comment) {
            return ResponseHandler::errorResponse(__('messages.not-found'), 404);
        }

        $like = Like::where('likable_id', $commentId)
            ->where('likable_type', get_class($comment))
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
            $like->likable_id = $commentId;
            $like->likable_type = get_class($comment);
            $like->created_at = now();
            $like->save();
            // set the status
            $status = 'liked';
        }

        return ResponseHandler::successResponse(null, ['status' => $status, 'comment_id' => $commentId]);
    }

    // ------------------------------------- get comment likes -------------------------------------

    public function getCommentLikes($commentId)
    {
        // validate the id
        $validator = Validator::make(['comment_id' => $commentId], [
            'comment_id' => 'required|numeric'
        ]);
        if ($validator->fails()) {
            return ResponseHandler::errorResponse($validator->errors()->first(), 400);
        }

        // get the comment & check if the comment exists
        $comment = Comment::find($commentId);
        if (!$comment) {
            return ResponseHandler::errorResponse(__('messages.not-found'), 404);
        }

        // get the comment likes
        $likes = Like::where('likable_id', $commentId)
            ->where('likable_type', get_class($comment))
            ->with(['user:id,name']) // Specify columns for related model
            ->select('id','user_id')
            ->get();

        // Check if there are no likes
        if ($likes->isEmpty()) {
            return ResponseHandler::successResponse("This comment has no likes yet", ['likes' => []]);
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
