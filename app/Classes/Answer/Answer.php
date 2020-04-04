<?php

namespace App\Classes\Answer;

class Answer {

	const TOKEN = '1113630730:AAGoTlQDqb9EncCzJBd6WlxSHzl_CPP3Ll8';

	/**
	 * Отправка сообщений
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
					'chat_id'      => $chatId,
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

		return $response['result'];
	}

	//	public function tgStart (string $token, int $chatId, string $text) : ? array {
	//
	//		$inline_button1 = array("text"=>"Привет", "callback_data"=>'/hi', "url"=>"https://ariscar.ru/qwe");
	//		$inline_button2 = array("text"=>"Пока", "callback_data"=>'/bye');
	//		$inline_keyboard = [[$inline_button1, $inline_button2]];
	//		$keyboard=array("keyboard"=>$inline_keyboard, "resize_keyboard" => true);
	//		$replyMarkup = json_encode($keyboard);
	//
	//		$url = "https://api.telegram.org/bot$token/sendMessage?" . http_build_query([
	//				'chat_id' => $chatId,
	//				'reply_markup' => $replyMarkup,
	//				'text'    => $text
	//			]);
	//
	//		$curl = curl_init($url);
	//		curl_setopt_array($curl, [
	//			CURLOPT_RETURNTRANSFER => TRUE,
	//			CURLOPT_CONNECTTIMEOUT => 5,
	//			CURLOPT_TIMEOUT        => 10,
	//
	//			/**
	//			 * Используется прокси, т.к. доступ напрямую заблокирован РКН
	//			 *
	//			 * WARNING Прокси необходимо продлять
	//			 * IPV6 proxy куплены у ipv6.zone, после покупки необходимо сменить тип на SOCKS5.
	//			 */
	//			CURLOPT_PROXY        => '[81.177.26.117]',
	//			CURLOPT_PROXYPORT    => 23306,
	//			CURLOPT_PROXYTYPE    => CURLPROXY_SOCKS5,
	//			CURLOPT_IPRESOLVE    => CURL_IPRESOLVE_V6,
	//			CURLOPT_PROXYUSERPWD => 'dgHrSS:yF5CtU'
	//		]);
	//
	//		$response = curl_exec($curl);
	//
	//		if ($response === FALSE) {
	//			curl_close($curl);
	//			return NULL;
	//		}
	//
	//		$httpCode = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
	//		curl_close($curl);
	//
	//		if ($httpCode !== 200)
	//			return NULL;
	//
	//		$response = json_decode($response, TRUE);
	//
	//		return $response['result'];
	//	}


}