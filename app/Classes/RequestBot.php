<?php

namespace App\Classes;

use App\Classes\Answer\Answer;
use App\Classes\Command\Command;
use App\Classes\Reaction\Reaction;
use App\Models\Message\Message;

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
	 * @throws \Exception
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
	 * @throws \Exception
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
			return 'Здравствуйте, Вы начали использовать бота!';
		//			$this->getAnswer()->tgSend($chat, 'Здравствуйте, Вы начали использовать бота!', TRUE);

		// Для тестирования
		return $send;
		//		$this->getAnswer()->tgSend($chat, $send);
		//		return 'ok';
	}

	/**
	 * Получения реакций
	 *
	 * @param array $request
	 * @return null|string
	 * @throws \Exception
	 */
	private function requestReaction (array $request) :? string {
		if (!isset($request['message']))
			return NULL;

		$chat     = (int)$request['message']['chat']['id'];
		$user     = (int)$request['message']['from']['id'];
		$message  = $request['message']['text'];
		$language = $request['message']['from']['language_code'] ?? 'ru';

		if (isset($request['message']['from']['username']))
			$nicknameOrName = $request['message']['from']['username'];

		else
			$nicknameOrName = $request['message']['from']['first_name'];

		$Reaction = new Reaction($user, $chat, $language);
		Message::insert(['user_id'    => $user,
		                 'chat_id'    => $chat,
		                 'nickname'   => $nicknameOrName,
		                 'message'    => $message,
		                 'created_at' => \Carbon\Carbon::now()
		]);

		// Для тестирования
		return $Reaction->getReaction($nicknameOrName, $message);
		//		$this->getAnswer()->tgSend($chat, $Reaction->getReaction($nicknameOrName, $message));
		//		return 'ok';
	}
}