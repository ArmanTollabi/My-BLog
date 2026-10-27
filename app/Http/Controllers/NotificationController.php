<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function read($notification){
        $notification = auth()->user()->notifications()->findOrFail($notification);
        $notification->markAsRead();
        if(isset($notification->data['post_id'])) {
            return redirect('/posts/' . $notification->data['post_id']);
        }
        return back();


    }
}
