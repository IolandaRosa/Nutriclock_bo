<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = ['senderId', 'senderName', 'senderPhotoUrl', 'receiverId', 'receiverName', 'receiverPhotoUrl', 'message', 'refMessageId', 'read'];
}
