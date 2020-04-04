<?php

namespace App\Classes\Message;

use App\Classes\Date;
use App\Models\Message\Message as ModelMessage;
use Illuminate\Support\Collection;
use stdClass;

class Message {

	protected $user;
	protected $chat;
	protected $ModelMessage;
	protected $Date;

	public function __construct (int $user, int $chat) {
		$this->user         = $user;
		$this->chat         = $chat;
		$this->ModelMessage = new ModelMessage();
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
	 * @return ModelMessage
	 */
	public function getModelMessage () : ModelMessage {
		return $this->ModelMessage;
	}

	/**
	 * @param ModelMessage $ModelMessage
	 */
	public function setModelMessage (ModelMessage $ModelMessage) {
		$this->ModelMessage = $ModelMessage;
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
		$Message = $this->getModelMessage()->lastMessage($this->getUser(), $this->getChat());
		return $Message ? $this->message($Message) : [];
	}

	/**
	 * Количество всех сообщений
	 *
	 * @return int
	 */
	public function countMessage () : int {
		return $this->getModelMessage()->countMessage($this->getUser(), $this->getChat());
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

		$messages = $this->getModelMessage()->lastMessages($this->getUser(), $this->getChat(), $limit);
		return $messages ? $this->messages($messages) : ['message'    => [],
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
	 * @param stdClass $Message
	 * @return array
	 */
	private function message (stdClass $Message) : array {
		return [
			'user'     => $Message->user_id,
			'chat'     => $Message->chat_id,
			'nickname' => $Message->nickname,
			'message'  => $Message->message,
			'date'     => $Message->created_at,
		];
	}

}