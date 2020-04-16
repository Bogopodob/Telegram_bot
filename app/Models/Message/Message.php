<?php

namespace App\Models\Message;


use Illuminate\Database\Eloquent\Model;

class Message extends Model {
	protected $table = 'chats';
	protected $primaryKey = 'id_chat';
	protected $attributes = ['user_id', 'chat_id', 'nickname', 'message'];
}