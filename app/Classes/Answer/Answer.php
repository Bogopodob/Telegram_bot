<?php

namespace App\Classes\Answer;

use Couchbase\Exception;
use Illuminate\Support\Facades\Lang;

class Answer {

	public function __construct (string $lang = 'ru') {
		Lang::setLocale($lang);
	}

	/**
	 * Отправка сообщений
	 *
	 * @param int    $chatId
	 * @param string $text
	 * @param bool   $start
	 * @return array|null
	 * @throws \Couchbase\Exception
	 */
	public function tgSend (int $chatId, string $text, bool $start = FALSE) :? array {

		$url = 'https://api.telegram.org/bot' . env('TOKEN_TELEGRAM') . '/sendMessage?' . http_build_query([
				'chat_id'      => $chatId,
				'reply_markup' => $start ? $this->tgStartBtn(Lang::get('messages.hi'),
					Lang::get('messages.bye')) : NULL,
				'text'         => $text
			]);

		$curl = curl_init($url);
		curl_setopt_array($curl, [
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_CONNECTTIMEOUT => 5,
			CURLOPT_TIMEOUT        => 10,

			/**
			 * Используется прокси, т.к. доступ напрямую заблокирован РКН
			 *
			 * WARNING Прокси необходимо продлять
			 * IPV6 proxy куплены у ipv6.zone, после покупки необходимо сменить тип на SOCKS5.
			 */
			CURLOPT_PROXY          => '[81.177.26.117]',
			CURLOPT_PROXYPORT      => 23306,
			CURLOPT_PROXYTYPE      => CURLPROXY_SOCKS5,
			CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V6,
			CURLOPT_PROXYUSERPWD   => 'dgHrSS:yF5CtU'
		]);

		$response = curl_exec($curl);

		if ($response === FALSE) {
			curl_close($curl);
			return NULL;
		}

		$httpCode = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);

		if ($httpCode !== 200)
			return NULL;

		$response = json_decode($response, TRUE);

		try {
			if (\is_array($response))
				return $response['result'];
		}
		catch (\Exception $e) {
			return NULL;
		}

		throw new Exception();
	}

	private function tgStartBtn (string $btnOne, string $btnTwo) : string {
		$inline_button1  = ['text' => "\xE2\x9C\x8C$btnOne"];
		$inline_button2  = ['text' => "\xE2\x9C\x8B$btnTwo"];
		$inline_keyboard = [[$inline_button1, $inline_button2]];
		$keyboard        = ['keyboard' => $inline_keyboard, 'resize_keyboard' => TRUE];
		return json_encode($keyboard);
	}
}