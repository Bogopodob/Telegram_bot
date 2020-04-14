<?php

namespace App\Classes\Reaction;

use App\Classes\Command\Command;
use App\Classes\Supporting\Date;
use App\Classes\Supporting\StringFormat;

class Reaction {

	protected $Command;
	protected $Date;
	protected $StringFormat;

	public function __construct (int $user, int $chat) {
		$this->Command      = new Command($user, $chat);
		$this->Date         = new Date();
		$this->StringFormat = new StringFormat();
	}

	/**
	 * @return StringFormat
	 */
	public function getStringFormat () : StringFormat {
		return $this->StringFormat;
	}

	/**
	 * @param StringFormat $StringFormat
	 */
	public function setStringFormat (StringFormat $StringFormat) {
		$this->StringFormat = $StringFormat;
	}

	/**
	 * @return Command
	 */
	public function getCommand () : Command {
		return $this->Command;
	}

	/**
	 * @param Command $Command
	 */
	public function setCommand (Command $Command) {
		$this->Command = $Command;
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

		$rMath = $this->reactionMath($message);
		if ($rMath)
			return $rMath;

		$cMath = $this->reactionCommandMath($nicknameOrName, $message);
		if ($cMath)
			return $cMath;

		$cLast = $this->reactionCommandLast($nicknameOrName, $message);
		if ($cLast)
			return $cLast;

		$cHelp = $this->reactionCommandHelp($nicknameOrName, $message);
		if ($cHelp)
			return $cHelp;

		$cStart = $this->reactionCommandStart($nicknameOrName, $message);
		if ($cStart)
			return $cStart;

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

		$arMessage = $this->getCommand()->getMessage()->getLastMessage();
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

		return 'Пока @' . $nicknameOrName . ', будем ждать Вас снова, вы уже написали ' . $this->getCommand()->getMessage()->countMessage() . ' сообщений.';
	}

	/**
	 * Ищет текст формата 56*87 -p 5 Где -p показать остаток после нуля
	 * Дробные числа могут идти как с , так и с .
	 *
	 * @param string $message
	 * @return null|string
	 */
	private function reactionMath (string $message) :? string {
		$math = $this->getStringFormat()->deleteSpaces($message, '');
		if (!preg_match('/(\d+|\d+(?:\.|\,)\d+)(\*|\+|\/|\-|\×|\÷)(\d+|\d+(?:\.|\,)\d+)(?:$|\-p(\d+))/ui', $math,
			$mMath))
			return NULL;

		$precision = NULL;
		if (\count($mMath) === 5)
			$precision = (int)$mMath[4];

		if (\count($mMath) === 4)
			$precision = 2;

		if ($precision === NULL)
			return NULL;

		$numberOne = (float)$this->getStringFormat()->replaceSymbol(',', '.', $mMath[1]);
		$numberTwo = (float)$this->getStringFormat()->replaceSymbol(',', '.', $mMath[3]);
		$symbol    = (string)$mMath[2];
		return $this->getStringFormat()->numberFormat($this->getCommand()->mathOperations($symbol, $numberOne,
			$numberTwo), $precision);
	}

	/**
	 * @param string $nicknameOrName
	 * @param string $message
	 * @return null|string
	 */
	private function reactionCommandMath (string $nicknameOrName, string $message) :? string {
		if (!preg_match('/^Math$/ui', $message))
			return NULL;

		return "@$nicknameOrName, возможно Вы имели в виду команду /math?";
	}

	/**
	 * @param string $nicknameOrName
	 * @param string $message
	 * @return null|string
	 */
	private function reactionCommandLast (string $nicknameOrName, string $message) :? string {
		if (!preg_match('/^last$/ui', $message))
			return NULL;

		return "@$nicknameOrName, возможно Вы имели в виду команду /last?";
	}

	/**
	 * @param string $nicknameOrName
	 * @param string $message
	 * @return null|string
	 */
	private function reactionCommandHelp (string $nicknameOrName, string $message) :? string {
		if (!preg_match('/^help$/ui', $message))
			return NULL;

		return "@$nicknameOrName, возможно Вы имели в виду команду /help?";
	}

	/**
	 * @param string $nicknameOrName
	 * @param string $message
	 * @return null|string
	 */
	private function reactionCommandStart (string $nicknameOrName, string $message) :? string {
		if (!preg_match('/^start$/ui', $message))
			return NULL;

		return "@$nicknameOrName, возможно Вы имели в виду команду /start?";
	}
}