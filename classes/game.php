<?php
/**
* Класс для обработки игры, ее ходов, записи
*/
class Game
{
	private $game_session;
	private $game;
	public $db;
	private $difficulty;

	function __construct($game_session)
	{
		if (isset($game_session)) {
			$this->game_session = $game_session;
		} else {
			throw new Exception('Game Error', 11);
		}
		global $db;
		$this->db = $db;
		$this->difficulty = 0;
	}

	//	Загрузка текущей игры по присвоенному игрвому и ID
	public function loadGame()
	{
		$query = "SELECT * FROM game WHERE s_id='{$this->game_session['s_id']}'";
		$result = $this->db->query($query);

		if(($game = $result->fetch(PDO::FETCH_ASSOC)) == false){
			return false;
		}

		$this->setGame($game);

		return true;
	}
	
	//	Создать новую игру и записать ее в БД
	public function createGame()
	{
		if (rand(0,1)) {
			$x = $this->game_session['player_1'];
			$o = $this->game_session['player_2'];
		} else {
			$x = $this->game_session['player_2'];
			$o = $this->game_session['player_1'];
		}

		$game = array(
			's_id' => $this->game_session['s_id'],
			'x' => $x,
			'o' => $o,
			'field' => serialize($this->createField()),
			'difficulty' => $this->difficulty
			);

		$query = sprintf("INSERT INTO game (s_id, x, o, field, difficulty) VALUES ('%s', '%s', '%s', '%s', '%s')",
				$game['s_id'], $game['x'], $game['o'], $game['field'], $game['difficulty']
			);
		$this->db->query($query);

		$this->setGame($game);
	}

	//	Установить сложность ИИ
	public function setDifficulty($dif)
	{
		$this->difficulty = $dif;
	}

	//	Внутрення функция для перевода массива с полем из БД
	private function setGame($game)
	{
		$game['field'] = unserialize($game['field']);
		$this->game = $game;
	}

	//	Получить игру, предварительно записав ее в БД
	public function getGame()
	{	
		$this->uploadGame();
		return $this->game;
	}
	
	//	Загрузить игру в БД
	public function uploadGame()
	{
		$query = sprintf('UPDATE game SET x="%s", o="%s", field=\'%s\' WHERE s_id="%s"',
						$this->game['x'], $this->game['o'], serialize($this->game['field']), $this->game['s_id']);
		$this->db->query($query);
	}

	//	Проверить должен ли ходить игрок
	public function isMove()
	{
		$counter = $this->countMoves($this->game['field']);
		if ($counter == 0) {
			return session_id() == $this->game['x'] ? true : false;
		} elseif ($counter == 9){
			return false;
		} else {
			$last_move = $counter%2 ? 'x' : 'o';

			return ('x' == $last_move && $this->game['x'] == session_id()) ^ ('x' != $last_move && $this->game['x'] != session_id()) ? false : true; 
		}
	}

	//	Записать ход
	public function writeMove($i, $j)
	{
		$type = $this->countMoves($this->game['field'])%2 ? 'o' : 'x';

		$this->game['field'][$i][$j] = $type;
	}

	//	Сосчитать число ходов в игре
	private function countMoves($field)
	{
		$counter = 0;

		for ($i=1; $i <= 3; $i++) { 
			for ($j=1; $j <= 3; $j++) { 
				if (!empty($field[$i][$j])) {
					$counter++;
				}
			}
		}

		return $counter;
	}

	//	Проверить игру на победу/поражение
	//	если истина, то вернуть массив со выигравшей строкой и победителья (х или о)
	private function checkWin()
	{
		require_once APP_PATH.'classes/win_lines.php';

		$win_lines = new WinLines($this->game['field']);
			return ($win = $win_lines->checkWin()) ? $win : false;
	}

	//	Получить текущий статус(состояние) игры
	public function getGameStatus()
	{
		/*
		* Игровые состояния
		* 1 - победил текующий пользователь
		* 2 - победил противник
		* 3 - ничья
		* 4 - игра продолжается
		*/
		$status = array();

		if(($status = $this->checkWin()) !== false){
			$status['status'] = (($status['winner'] == 'x' && $this->game['x'] == session_id()) ^ ($status['winner'] != 'x' && $this->game['x'] != session_id())) ? 1 : 2;
		} elseif ($this->countMoves($this->game['field']) == 9) {
			$status['status'] = 3;
		} else {
			$status['status'] = 4;
			$status['is_move'] = $this->isMove();
		}
		return $status;
	}

	//	Создать пустое игровое поле в виде массива
	private function createField()
	{
		for ($i=1; $i <= 3; $i++) { 
			for ($j=1; $j <= 3; $j++) { 
				$field_array[$i][$j] = "";
			}
		}
		return $field_array;
	}

}
?>