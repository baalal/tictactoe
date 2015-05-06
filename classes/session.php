<?php 
/**
* This class create/connect/destroy game session
*/
class Session
{
	public $db;
	private $game_session;

	function __construct()
	{
		session_start();

		global $db;
		$this->db = $db;
	}

	public function connectCurrentSession()
	{
		$query = "SELECT * FROM session WHERE s_id='{$_SESSION['s_id']}'";
		$result = $this->db->query($query);

		$this->game_session = $result->fetch(PDO::FETCH_ASSOC);
	}

	//	Обновить сессию в БД
	private function setGameSession($game_session)
	{
		$query = "UPDATE session SET 
		player_1 = '{$game_session['player_1']}', 
		player_2 = '{$game_session['player_2']}', 
		status = '{$game_session['status']}',
		type = '{$game_session['type']}'
		WHERE s_id='{$game_session['s_id']}'";	

		$this->db->query($query);

		$this->game_session = $game_session;
	}

	//	Получить игровую сессию
	public function getGameSession()
	{	
		return $this->game_session;
	}

	//	Подключиться к открытой сессии
	private function connectOpenedSession($game_session)
	{
		if (empty($game_session['player_1'])) {
			$game_session['player_1'] = session_id();
		} else {
			$game_session['player_2'] = session_id();
		}

		if(!empty($game_session['player_1']) && !empty($game_session['player_2']))
			$game_session['status'] = 'f';

		$_SESSION['s_id'] = $game_session['s_id'];
		$this->setGameSession($game_session);

		return true;
	}

	//	Подключитсья к открытой сессии удовлетворяющую запрос
	public function connectNewSession($type)
	{
		switch ($type) {
			case '1':
				$enemy = 'player';
				break;
			case '2':
				$enemy = 'AI';
				break;
			default:
				return false;
				break;
		}
		
		//	поиск подходящей сессии
		$query = "SELECT * FROM session WHERE status='o' AND type='$enemy'";
		$result = $this->db->query($query);
		
		if(!$result->rowCount()){
			$this->createNewSession($enemy);	//	Если нет открытой сессии - создаем
			
			$query = "SELECT * FROM session WHERE status='o' AND type='$enemy'";	//	Тащим ид новой сессии
			$result = $this->db->query($query);				
		}
		//	подключаемся
		$this->connectOpenedSession($result->fetch(PDO::FETCH_ASSOC));
	}

	//	Создать новую сессию подходящую запросу
	protected function createNewSession($type)
	{
		if ($type == "player") {
			$query = "INSERT INTO session (status, type) VALUES ('o', 'player')";
		} else {
			$query = "INSERT INTO session (status, player_2, type) VALUES ('o', 'AI', 'AI')";	
		}
		
		$this->db->query($query);
	}

	//	Закрыть игровую сессию, удалить данные из php-сессии
	public function closeSession()
	{
		if(!$this->endGame()) $this->game_session = null;
		session_unset();
		session_destroy();
	}

	//	Завершить игру
	public function endGame()
	{	
		if (isset($this->game_session) && $this->game_session['status'] != 'c') {
			$this->game_session['status'] = 'c';
			$this->setGameSession($this->game_session);
			$this->game_session = null;
			return true;
		} else {
			return false;
		}
	}
}
?>