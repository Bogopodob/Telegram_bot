<?php

namespace App\Classes\Answer;

use Couchbase\Exception;

class Answer {

	const TOKEN = '1113630730:AAGoTlQDqb9EncCzJBd6WlxSHzl_CPP3Ll8';

	/**
	 * Отправка сообщений
	 *
	 * @param int    $chatId
	 * @param string $text
	 * @param bool   $start
	 * @return array|null
	 */
	public function tgSend (int $chatId, string $text, bool $start = FALSE) : ? array {

		if ($start) {
			$inline_button1  = ["text" => "Привет", "callback_data" => '/hi', "url" => "https://ariscar.ru/qwe"];
			$inline_button2  = ["text" => "Пока", "callback_data" => '/bye'];
			$inline_keyboard = [[$inline_button1, $inline_button2]];
			$keyboard        = ["keyboard" => $inline_keyboard, "resize_keyboard" => TRUE];
			$replyMarkup     = json_encode($keyboard);

			$url = "https://api.telegram.org/bot" . self::TOKEN . "/sendMessage?" . http_build_query([
					'chat_id'      => $chatId,
					'reply_markup' => $replyMarkup,
					'text'         => $text
				]);
		}
		else
			$url = "https://api.telegram.org/bot" . self::TOKEN . "/sendMessage?" . http_build_query([
					'chat_id' => $chatId,
					'text'    => $text
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

		return $response['result'];
	}

	/**
	 * Переводчик
	 *
	 * @param string $text
	 * @param string $language
	 * @return null|string
	 * @throws Exception
	 */
	public function translator (string $text, string $language) : ? string {

		$url = 'https://translate.yandex.net/api/v1.5/tr.json/translate?' . http_build_query([
				'key' => 'trnsl.1.1.20200409T200847Z.48b4d30e03804665.0c07b5438785a3bf0fce5808bb76a3d0ab89545a',
				'text' => $text,
				'lang'    => 'ru-'.$language,
				'format' => 'plain',
				'options' => 1,
			]);

		$curlObject = curl_init();

		curl_setopt($curlObject, CURLOPT_URL, $url);

		curl_setopt($curlObject, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curlObject, CURLOPT_SSL_VERIFYHOST, false);

		curl_setopt($curlObject, CURLOPT_RETURNTRANSFER, true);

		$responseData = curl_exec($curlObject);

		curl_close($curlObject);

		if ($responseData === false) {
			throw new Exception('Response false');
		}

		return $responseData;
	}
}