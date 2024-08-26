<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Str;

class NotificationService
{
    // ------------------------------------------------------- Generate Notification Data -------------------------------------------------------
    public static function getNotificationData($type, $data)
    {
        switch ($type) {
                // ------------------------------ new post notification ------------------------------
            case 'new-comment':
                return [
                    "mailData" => [
                        "subject" => __('views.new-comment.title'),
                        "view" => "new_comment",
                        "data" => $data
                    ],
                ];


            default:
                return null;
        }
    }
}
