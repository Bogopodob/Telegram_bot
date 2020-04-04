<?php

namespace App\Classes\Reaction;

use App\Classes\Date;
use App\Classes\Message\Message;

class Reaction {

	protected $Message;
	protected $Date;

	public function __construct (int $user, int $chat) {
		$this->Message = new Message($user, $chat);
		$this->Date    = new Date();
	}

	/**
	 * @return Message
	 */
	public function getMessage () : Message {
		return $this->Message;
	}

	/**
	 * @param Message $Message
	 */
	public function setMessage (Message $Message) {
		$this->Message = $Message;
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
	 * Получение реакции
	 *
	 * @param string $nicknameOrName
	 * @param string $message
	 * @return null|string
	 */
	public function getReaction (string $nicknameOrName = '', string $message) :? string {
		$rHi = $this->reactionHi($nicknameOrName, $message);
		if ($rHi)
			return $rHi;

		$rBye = $this->reactionBye($nicknameOrName, $message);
		if ($rBye)
			return $rBye;

		return NULL;
	}

	/**
	 * Реакция на сообщение "Привет"
	 *
	 * @param string $nicknameOrName
	 * @param string $message
	 * @return null|string
	 */
	private function reactionHi (string $nicknameOrName, string $message) :? string {
		if (!preg_match('/^привет$/ui', $message))
			return NULL;

		$arMessage = $this->getMessage()->getLastMessage();
		$date      = 'так как Вы еще не писали сообщений я не могу сказать дату последнего сообщения';
		if ($arMessage)
			$date = 'последний раз ты писал мне ' . $this->getDate()->fullDataFormat($arMessage['date']);

		return 'Привет @' . $nicknameOrName . ', рад тебя видеть, ' . $date;

	}

	/**
	 * Реакция на сообщение "Пока"
	 *
	 * @param string $nicknameOrName
	 * @param string $message
	 * @return null|string
	 */
	private function reactionBye (string $nicknameOrName, string $message) :? string {
		if (!preg_match('/^Пока$/ui', $message))
			return NULL;

		return 'Пока @' . $nicknameOrName . ', будем ждать Вас снова, вы уже написали ' . $this->getMessage()->countMessage() . ' сообщений.';
	}
}