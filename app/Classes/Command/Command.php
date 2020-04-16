<?php

namespace App\Classes\Command;

use App\Classes\Message\Message;
use App\Classes\Supporting\StringFormat;
use Exception;

class Command {

	protected $Message;
	protected $user;
	protected $chat;
	protected $StringFormat;

	public function __construct (int $user, int $chat) {
		$this->Message      = new Message($user, $chat);
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
	 * @throws \Exception
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

		$cHelp = $this->commandHelp($command);
		if ($cHelp)
			return $cHelp;

		return 'Данная команда не существует!';
	}

	/**
	 * Команда /last n - число сообщений которые нужно вывести
	 *
	 * @param string $command
	 * @return null|string
	 */
	private function commandLast (string $command) :? string {
		$command = $this->getStringFormat()->deleteSpaces($command, '');
		if (!preg_match('/^\/last?:\s+|(\d+)$/ui', $command, $mLimit))
			return NULL;

		$message = $this->getMessage()->lastMessage((int)$mLimit[1]);
		return $message['messageStr'];
	}

	/**
	 * Команда /start запускает работу бота
	 *
	 * @param string $command
	 * @return null|string
	 */
	private function commandStart (string $command) :? string {
		$command = $this->getStringFormat()->deleteSpaces($command, '');
		if (!preg_match('/^\/start$/ui', $command))
			return NULL;

		return 'start';
	}

	/**
	 * Данное регулярное выражение может принимать строку в виде:
	 * /math(5,4*8.4) ответ, массив значений из 4 элементов [/div(5,4*8.4), 5.4, *, 8.4];
	 * Дробные числа могут идти как с , так и с .
	 *
	 * @param string $command
	 * @return null|string
	 * @throws \Exception
	 */
	private function commandMath (string $command) :? string {
		$command = $this->getStringFormat()->deleteSpaces($command, '');
		if (preg_match('/^\/math$/ui', $command))
			return 'Пример использование команды /math(3.54*69.41)';

		else if (!preg_match('/^\/math\((\d+|\d+(?:\.|\,)\d+)(?:(\*|\+|\/|\-|\×|\÷))(\d+|\d+(?:\.|\,)\d+)\)$/ui',
			$command, $mResult))
			return NULL;

		if (\count($mResult) === 4)
			return $this->mathOperations((string)$mResult[2], (float)$mResult[1], (float)$mResult[3]);

		return NULL;
	}

	/**
	 * @param string $command
	 * @return null|string
	 */
	private function commandHelp (string $command) :? string {
		$command = $this->getStringFormat()->deleteSpaces($command, '');
		if (!preg_match('/^\/help$/ui', $command, $mLimit))
			return NULL;

		return "Основные комманды:\n /last n список последних сообщений.\n /math(x*y) знак * математическая операция \n\n **********************\n\n Инструкция по использованию:\n /last n - где n - чисто последних сообщений, n не обязательный параметр, по умолчанию показывает 5 последних сообщений (пример: /last 5). \n math(x,*,y) где x - первая цифра, y - вторая цифра, * - математическая операция (/, *, -, +), пример: /math(5+8). \n !!Но можно пользоваться и без команды /math. x*y -p n где где x - первая цифра, y - вторая цифра, * - математическая операция (/, *, -, +), n - округление числа после запятой, пример 5+9-p 5!!";
	}

	/**
	 * Общая функция обработки математический операций
	 *
	 * @param string $symbol
	 * @param float  $numberOne
	 * @param float  $numberTwo
	 * @return string
	 * @throws Exception
	 */
	public function mathOperations (string $symbol, float $numberOne, float $numberTwo) : string {
		if ($symbol === '+')
			return $numberOne + $numberTwo;

		else if ($symbol === '-')
			return $numberOne - $numberTwo;

		else if ($symbol === '*' || $symbol === '×')
			return $numberOne * $numberTwo;

		try {
			if ($symbol === '/' || $symbol === '÷')
				return $this->division($numberOne, $numberTwo);

		} catch (Exception $e) {
			return $e->getMessage();
		}

		throw new Exception('Неизвестный символ!');
	}

	/**
	 * Деления
	 *
	 * @param float $numberOne
	 * @param float $numberTwo
	 * @return float
	 * @throws Exception
	 */
	private function division (float $numberOne, float $numberTwo) : float {
		if (!$numberTwo)
			throw new Exception('Деление на ноль!');

		return $numberOne / $numberTwo;
	}

}