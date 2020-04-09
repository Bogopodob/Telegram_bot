<?php

namespace App\Classes\Command;

use App\Classes\Message\Message;

class Command {

	protected $Message;
	protected $user;
	protected $chat;

	public function __construct (int $user, int $chat) {
		$this->Message = new Message($user, $chat);
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
	 * Общая функция обработки команд
	 *
	 * @param string $command
	 * @return null|string
	 */
	public function getCommand (string $command) :? string {
		$cLast = $this->commandLast($command);
		if ($cLast)
			return $cLast;

		$cMath = $this->commandMath($command);
		if ($cMath)
			return $cMath;

		$cStart = $this->commandStart($command);
		if ($cStart)
			return $cStart;

		return 'Данная команда не существует!';
	}

	/**
	 * Команда /last n - число сообщений которые нужно вывести
	 *
	 * @param string $command
	 * @return null|string
	 */
	private function commandLast (string $command) :? string {
		$command = $this->deleteSpaces($command, '');
		if (!preg_match('/^\/last?:\s+|(\d+)$/ui', $command, $mLimit))
			return NULL;

		$message = $this->getMessage()->lastMessage($mLimit[1]);
		return $message['messageStr'];
	}

	/**
	 * Команда /start запускает работу бота
	 *
	 * @param string $command
	 * @return null|string
	 */
	private function commandStart (string $command) :? string {
		$command = $this->deleteSpaces($command, '');
		if (!preg_match('/^\/start$/ui', $command))
			return NULL;

		return 'start';
	}

	/**
	 * Данное регулярное выражение может принимать строку в виде:
	 * /math(5,4*8.4) ответ, массив значений из 4 элементов [/div(5,4*8.4), 5.4, *, 8.4];
	 *
	 * @param string $command
	 * @return null|string
	 */
	private function commandMath (string $command) :? string {
		$command = $this->deleteSpaces($command, '');
		if (!preg_match('/^\/math\((\d+|\d+(?:\.|\,)\d+)(?:(\*|\+|\/|\-|\×|\÷))(\d+|\d+(?:\.|\,)\d+)\)$/ui', $command, $mResult))
			return NULL;

		if (\count($mResult) === 4)
			return $this->mathOperations((string)$mResult[2], (float)$mResult[1], (float)$mResult[3]);

		else if (\count($mResult) === 5)
			return $this->mathOperations('/', (float)$mResult[1], (float)$mResult[4]);

		return NULL;
	}

	/**
	 * Удаление пробелов
	 *
	 * @param string $string
	 * @param string $spaces
	 * @return string
	 */
	private function deleteSpaces (string $string, string $spaces = ' ') : string {
		return $string ? preg_replace('/\s+/', $spaces, $string) : '';
	}

	/**
	 * Общая функция обработки математический операций
	 *
	 * @param string $symbol
	 * @param float  $numberOne
	 * @param float  $numberTwo
	 * @return null|string
	 */
	public function mathOperations (string $symbol, float $numberOne, float $numberTwo) :? string {
		if ($symbol === '+')
			return $this->plus($numberOne, $numberTwo);

		else if ($symbol === '-')
			return $this->minus($numberOne, $numberTwo);

		else if ($symbol === '*' || $symbol === '×')
			return $this->multiply($numberOne, $numberTwo);

		else if ($symbol === '/' || $symbol === '÷')
			return $this->division($numberOne, $numberTwo);

		return NULL;
	}

	/**
	 * Плюс
	 *
	 * @param float $numberOne
	 * @param float $numberTwo
	 * @return string
	 */
	private function plus (float $numberOne, float $numberTwo) : string {
		return $this->numberFormat($numberOne + $numberTwo);
	}

	/**
	 * Минус
	 *
	 * @param float $numberOne
	 * @param float $numberTwo
	 * @return string
	 */
	private function minus (float $numberOne, float $numberTwo) : string {
		return $this->numberFormat($numberOne - $numberTwo);
	}

	/**
	 * Умножения
	 *
	 * @param float $numberOne
	 * @param float $numberTwo
	 * @return string
	 */
	private function multiply (float $numberOne, float $numberTwo) : string {
		return $this->numberFormat($numberOne * $numberTwo);
	}

	/**
	 * Деления
	 *
	 * @param float $numberOne
	 * @param float $numberTwo
	 * @return string
	 */
	private function division (float $numberOne, float $numberTwo) : string {
		return $numberTwo ? $this->numberFormat($numberOne / $numberTwo) : 'Деление на ноль невозможно';
	}

	/**
	 * Форматирования чисел (25,569.00)
	 *
	 * @param        $number
	 * @param int    $balance
	 * @param string $balanceSymbol
	 * @param string $numberSymbol
	 * @return string
	 */
	private function numberFormat ($number, $balance = 2, $balanceSymbol = '.', $numberSymbol = ',') : string {
		return number_format($number, $balance, $balanceSymbol, $numberSymbol);
	}

}