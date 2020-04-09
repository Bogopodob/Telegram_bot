<?php

namespace App\Classes\Supporting;

class Date {

	/**
	 * Месяца
	 *
	 * @param int $month
	 * @return string
	 */
	public function month (int $month) : string {
		$arMonth = [
			'январа',
			'февраля',
			'марта',
			'апреля',
			'мая',
			'июня',
			'июля',
			'августа',
			'сентября',
			'октября',
			'ноября',
			'декабря'
		];
		return $arMonth[$month];
	}

	/**
	 * Дни недели
	 *
	 * @param int $dayWeek
	 * @return string
	 */
	public function dayWeek (int $dayWeek) : string {
		$arDayWeek = ['в понедельник', 'во вторник', 'в среду', 'в четверг', 'в пятницу', 'в субботу', 'в воскресенье'];
		return $arDayWeek[$dayWeek];
	}

	/**
	 * Получение форматированной даты (в субботу, 04 апреля 2020 г, в 00:28)
	 *
	 * @param string $date
	 * @return string
	 */
	public function fullDataFormat (string $date) : string {
		$dateYear  = date('Y', strtotime($date));
		$dateDay   = date('d', strtotime($date));
		$dateWeek  = (int)date('w', strtotime($date));
		$dateMonth = (int)date('m', strtotime($date));
		$dateTime  = date('H:i', strtotime($date));
		return "{$this->dayWeek(($dateWeek === 0) ?: $dateWeek - 1)}, $dateDay {$this->month(($dateMonth === 0) ?: $dateMonth - 1)} $dateYear г, в $dateTime";
	}
}