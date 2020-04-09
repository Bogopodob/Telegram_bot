<?php

namespace App\Classes\Supporting;

class StringFormat {
	/**
	 * @param string $string
	 * @param string $spaces
	 * @return string
	 */
	public function deleteSpaces (string $string, string $spaces = ' ') : string {
		return $string ? preg_replace('/\s+/', $spaces, $string) : '';
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
	public function numberFormat ($number, $balance = 2, $balanceSymbol = '.', $numberSymbol = ',') : string {
		return number_format($number, $balance, $balanceSymbol, $numberSymbol);
	}

	/**
	 * @param string $search
	 * @param string $replace
	 * @param string $string
	 * @return string
	 */
	public function replaceSymbol (string $search, string $replace, string $string) : string {
		return str_replace($search, $replace, $string);
	}
}