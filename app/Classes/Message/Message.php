<?php

namespace App\Classes\Message;

use App\Classes\Supporting\Date;
use App\Models\Message\Message as ModelMessage;
use Illuminate\Support\Collection;

class Message {

	protected $user;
	protected $chat;
	protected $Date;

	public function __construct (int $user, int $chat) {
		$this->user         = $user;
		$this->chat         = $chat;
		$this->Date         = new Date();
	}

	/**
	 * @return int
	 */
	public function getUser () : int {
		return $this->user;
	}

	/**
	 * @param int $user
	 */
	public function setUser (int $user) {
		$this->user = $user;
	}

	/**
	 * @return int
	 */
	public function getChat () : int {
		return $this->chat;
	}

	/**
	 * @param int $chat
	 */
	public function setChat (int $chat) {
		$this->chat = $chat;
	}

	/**
	 * @return Date
	 */
	public function getDate () : Date {
		return $this->Date;
	}

	/**
	 * @param Date $Date
	 */
	public function setDate (Date $Date) {
		$this->Date = $Date;
	}

	/**
	 * Получить предпоследнее сообщение
	 *
	 * @return array
	 */
	public function getLastMessage () : array {
		$Message = ModelMessage::where('user_id', $this->getUser())->where('chat_id', $this->getChat())->latest('id_chat')->skip(1)->limit(1)->first();
		return $Message ? $this->message($Message) : [];
	}

	/**
	 * Количество всех сообщений
	 *
	 * @return int
	 */
	public function countMessage () : int {
		return ModelMessage::where('user_id', $this->getUser())->where('chat_id', $this->getChat())->count();
	}

	/**
	 * Получить n - количество последних сообщений
	 *
	 * @param int $limit
	 * @return array
	 */
	public function lastMessage (int $limit = 5) : array {
		if ($limit <= 0)
			$limit = 5;

		$messages = ModelMessage::where('user_id', $this->getUser())->where('chat_id',
			$this->getChat())->latest()->skip(0)->limit($limit)->get();
		return $messages ? $this->messages($messages) : [
			'message'    => [],
			'messageStr' => 'У Вас нет не одного сообщения!'
		];
	}

	/**
	 * Перебор всех сообщений
	 *
	 * @param Collection $messages
	 * @return array
	 */
	private function messages (Collection $messages) : array {
		$arMessages = [];
		$messageStr = 'Сообщении: ';
		foreach ($messages as $Message) {
			$message                 = $this->message($Message);
			$arMessages['message'][] = $message;
			$messageStr              .= "{$message['nickname']}\n{$message['message']}\n{$this->getDate()->fullDataFormat($message['date'])}\n";
		}

		$arMessages['messageStr'] = $messageStr;

		return $arMessages;
	}

	/**
	 * Формирования массива
	 *
	 * @param ModelMessage $Message
	 * @return array
	 */
	private function message (ModelMessage $Message) : array {
		return [
			'user'     => $Message->user_id,
			'chat'     => $Message->chat_id,
			'nickname' => $Message->nickname,
			'message'  => $Message->message,
			'date'     => $Message->created_at,
		];
	}

}