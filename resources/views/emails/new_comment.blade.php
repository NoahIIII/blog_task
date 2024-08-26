<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('views.new-comment.title') }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .email-container {
            background-color: #ffffff;
            padding: 20px;
            margin: 40px auto;
            max-width: 600px;
            border-radius: 6px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header {
            padding-bottom: 10px;
            margin-bottom: 10px;
            border-bottom: 1px solid #eaeaea;
            text-align: left;
        }

        .content {
            padding: 20px 0;
            line-height: 1.5;
            color: #555;
        }

        .content p {
            margin: 0 0 15px;
        }

        .post-title {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }

        .comment-section {
            background-color: #f9f9f9;
            border-left: 3px solid #0073e6;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }

        .comment-author {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 5px;
            color: #0073e6;
        }

        .comment-content {
            font-style: italic;
            color: #555;
            margin-bottom: 5px;
        }

        .comment-time {
            font-size: 12px;
            color: #999;
        }

        .button {
            display: block;
            width: fit-content;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #0073e6;
            color: #ffffff;
            text-decoration: none;
            border-radius: 4px;
            text-align: center;
        }

        .button:hover {
            background-color: #005bb5;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            color: #777;
            margin-top: 20px;
        }
    </style>

</head>

<body>
    <div class="email-container">
        <div class="header">
            <strong>
                <p>{{ __('views.new-comment.header') }}</p>
            </strong>
        </div>
        <div class="content">
            <p>{{ __('views.global.hello') }} {{ $data['post_author'] }}</p>
            <p>{{ __('views.new-comment.comment-added') }} <strong>"{{ $data['post_title'] }}"</strong>.</p>
            <p>{{ __('views.new-comment.comment-details') }}</p>

            {{-- <p class="post-title">{{ $data['post_title'] }}</p> --}}

            <div class="comment-section">
                <p class="comment-author">{{ $data['comment_author'] }}</p>
                <p class="comment-content">"{{ $data['comment_content'] }}"</p>
                <p class="comment-time">{{ __('views.new-comment.commented-at') }} {{ $data['comment_time'] }}</p>
            </div>
        </div>
        <div class="footer">
            {{ __('views.global.contact-line') }}
        </div>
    </div>
</body>

</html>
