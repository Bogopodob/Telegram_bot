<?php

namespace App\Http\Controllers\Api\V1\Bot;

use App\Classes\RequestBot;
use App\Http\Controllers\Controller;

class BotController extends Controller {
	public function bot () {

		// ********************************
		// Перед запуском, в директории
		// ********************************
		// php artisan cache:creare
		// composer dump-autoload
		// php artisan migrate

		// @tomsk_innovation_test_bot - название бота
		// Примеры ответов от телеграмма
		// ** json когда пользователь вводит команду
		//    {"update_id":788689423,"message":{"message_id":26,"from":{"id":464546055,"is_bot":false,"first_name":"\u0415\u0432\u0433\u0435\u043d\u0438\u0439","last_name":"\u0411\u0430\u043b\u0438\u043a\u0430","username":"bogopodob","language_code":"ru"},"chat":{"id":464546055,"title":"bogopodob_test","type":"supergroup"},"date":1585957266,"text":"\/last 3","entities":[{"offset":0,"length":5,"type":"bot_command"}]}}
		// *** Когда пользователь пишет сообщения
		//    {"update_id":788689258,"message":{"message_id":174,"from":{"id":464546055,"is_bot":false,"first_name":"\u0415\u0432\u0433\u0435\u043d\u0438\u0439","last_name":"\u0411\u0430\u043b\u0438\u043a\u0430","username":"bogopodob","language_code":"ru"},"chat":{"id":464546055,"first_name":"\u0415\u0432\u0433\u0435\u043d\u0438\u0439","last_name":"\u0411\u0430\u043b\u0438\u043a\u0430","username":"bogopodob","type":"private"},"date":1585854250,"text":"\u041f\u0440\u0438\u0432\u0435\u0442"}}

		$data = file_get_contents('php://input');
		$data = json_decode($data, TRUE);

		$Bot = new RequestBot();
		return $Bot->request($data);
	}
}
