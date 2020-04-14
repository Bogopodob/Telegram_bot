<?php

namespace App\Classes;

use App\Classes\Answer\Answer;
use App\Classes\Command\Command;
use App\Classes\Reaction\Reaction;

class RequestBot {

	protected $Answer;

	public function __construct () {
		$this->Answer = new Answer();
	}

	/**
	 * @return Answer
	 */
	public function getAnswer () : Answer {
		return $this->Answer;
	}

	/**
	 * @param Answer $Answer
	 */
	public function setAnswer (Answer $Answer) {
		$this->Answer = $Answer;
	}

	/**
	 * Отправка сообщение в телеграм
	 *
	 * @param array $request
	 * @return null|string
	 */
	public function request (array $request) :? string {
		$command = $this->requestCommand($request);
		if ($command)
			return $command;

		$reaction = $this->requestReaction($request);
		if ($reaction)
			return $reaction;

		return NULL;
	}

	/**
	 * Получение команд
	 *
	 * @param array $request
	 * @return null|string
	 */
	private function requestCommand (array $request) :? string {
		if (!isset($request['message']['entities']))
			return NULL;

		$chat    = $request['message']['chat']['id'];
		$user    = $request['message']['from']['id'];
		$message = $request['message']['text'];

		$Command = new Command($user, $chat);
		$send    = $Command->getCommand($message);
		if ($send === 'start')

			// Для тестирования
			//			return 'send';
			return $this->getAnswer()->tgSend($chat, 'Здравствуйте, Вы начали использовать бота!', TRUE);

		// Для тестирования
		//		return $send;
		return $this->getAnswer()->tgSend($chat, $send);
	}

	/**
	 * Получения реакций
	 *
	 * @param array $request
	 * @return null|string
	 */
	private function requestReaction (array $request) :? string {
		if (!isset($request['message']))
			return NULL;

		$chat    = (int)$request['message']['chat']['id'];
		$user    = (int)$request['message']['from']['id'];
		$message = $request['message']['text'];

		if (isset($request['message']['from']['username']))
			$nicknameOrName = $request['message']['from']['username'];
		else $nicknameOrName = $request['message']['from']['username'];

		$Reaction = new Reaction($user, $chat);
		$Reaction->getCommand()->getMessage()->getModelMessage()->create($user, $chat, $nicknameOrName, $message);

		// Для тестирования
		//		return $Reaction->getReaction($nicknameOrName, $message);
		return $this->getAnswer()->tgSend($chat, $Reaction->getReaction($nicknameOrName, $message));
	}
}