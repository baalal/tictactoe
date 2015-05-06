<?php
error_reporting(0);
include 'config.php';

foreach (scandir(CLASS_PATH) as $class) {
	if(is_file(CLASS_PATH.$class)) include CLASS_PATH.$class;
}

const REPEAT_REQUEST = 1000; // ms

try{

$db = new DB();

} catch(PDOException $e){
	exit('Can\'t connect to database, please try again later');
}

try{

$Session = new Session();

$data = $_POST;

if(isset($_SESSION['s_id'])) {
	$Session->connectCurrentSession();				//	подключаемся к своей сессии
}

//	Если нужно закрыть сессию
switch($data['case']){
	case 'enemy':
		if(!isset($_SESSION['s_id']))
			$Session->connectNewSession($data['enemy']);
		break;
	case 'close_session':
		$Session->closeSession();
		break;
}

$game_session = $Session->getGameSession();

//	Если нет сессии, зачем идти дальше ?
if (empty($game_session)) {
	exit();
}


$View = new View();

//	Определяем действие по текущему состоянию сессии
switch ($game_session['status']) {
	case 'o':	//	Сессия открыта, т.е. есть свободное место. Делаем повторный запрос через некоторое время
		$View->repeatRequest(REPEAT_REQUEST, 'searching');
		break;
	case 'f':	//	Сессия полна - можно создать или подключиться к игре
	case 'c':	//	Сессия закрыта - получаем информацию об игре
		
		$Game = new Game($game_session);

		if(!$Game->loadGame()){	// Загружаем игру в объект, если ее нет - создаем
			if ($game_session['type'] == 'AI') {
				$Game->setDifficulty($data['difficulty']);	//	Устанавливаем сложность
			}
			$Game->createGame();
		}

		//	Если игрок ждет ход противника и противник ИИ, то ход делает ИИ
		if($data['case'] == 'waiting' && $game_session['type'] == 'AI'){
			
			$game = $Game->getGame();
			require_once CLASS_PATH.'AI_class.php';
	
			$ai = new AIclass($game);

			$move = $ai->generateMove($ai->field);

			$Game->writeMove($move['i'], $move['j']);
		}
		//	Если наш ход то получаем ход и записываем
		if ($data['case'] == 'move' && $Game->isMove()) {
			$Game->writeMove($data['i'], $data['j']);
		}
		//	Определяем игровое состояние
		$game_status = $Game->getGameStatus();

		if($game_status['status'] != 4){
			$Session->endGame();
		}

		$game = $Game->getGame();

		break;
	default:
		# code...
		break;
}

//	Выводим данные в зависимости от состояния
switch (@$game_status['status']) {
	case '4':
		if($View->isWait(!$game_status['is_move'])){
			$View->repeatRequest(REPEAT_REQUEST, 'waiting');
		}
		$View->loadField($game, $game_status['is_move']);
		$View->showHTML();
		break;
	case '1':
	case '2':
	case '3':
		$View->loadField($game, false);
		$View->endMessage($game_status);
		$View->showHTML();
		break;
	default:
		# code...
		break;
}	

} catch (Exception $e) {
	echo '<p>'.$e->getMessage().': '.$e->getCode().'</p>';
}
?>