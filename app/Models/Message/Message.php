<?php

namespace app\Models\Message;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use stdClass;

class Message extends Model {

	/**
	 * Получение предпоследнего сообщения, используется, чтобы получить дату
	 * @param int $user
	 * @param int $chat
	 * @return null|stdClass
	 */
	public function lastMessage (int $user, int $chat) :? stdClass {
		return DB::table('chats')->where('user_id', $user)->where('chat_id', $chat)->latest('id_chat')->skip(1)->limit(1)->first();
	}

	/**
	 * Подсчет всех сообщений
	 * @param int $user
	 * @param int $chat
	 * @return int
	 */
	public function countMessage (int $user, int $chat) : int {
		return DB::table('chats')->where('user_id', $user)->where('chat_id', $chat)->count();
	}

	/**
	 * Получить n - количество сообщений
	 * @param int $user
	 * @param int $chat
	 * @param int $limit
	 * @return \Illuminate\Support\Collection
	 */
	public function lastMessages (int $user, int $chat, int $limit) {
		return DB::table('chats')->where('user_id', $user)->where('chat_id',
			$chat)->latest()->skip(0)->limit($limit)->get();
	}

	/**
	 * Запись сообщений в бд, записывает только сообщения, не команды!
	 * @param int    $user
	 * @param int    $chat
	 * @param string $nickname
	 * @param string $message
	 */
	public function create (int $user, int $chat, string $nickname, string $message) : void {
		DB::table('chats')->insert([
			'user_id'    => $user,
			'chat_id'    => $chat,
			'nickname'   => $nickname,
			'message'    => $message,
			'created_at' => \Carbon\Carbon::now(),
		]);
	}

}