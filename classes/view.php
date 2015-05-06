<?php 
if (!defined('APP_PATH')) {
	exit();
}
/**
* Класс для вывода данных непосредственно клиенту
*/
class View
{
	private $user_id;
	private $html;

	function __construct()
	{
		$user_id = session_id();
	}

	//	Установить игру в объект
	public function setGame($game)
	{
		$this->game = $game;
	}

	//	Загрузить HTML-поле игры
	public function loadField($game, $is_move)
	{
		$field = $game['field'];
		$html = '';

		for ($i=1; $i <= 3; $i++) {
			$html .= '<tr>';
			for ($j=1; $j <= 3; $j++) {
				if($field[$i][$j] == 'x'){
					$value = '<td><img src="'.APP_URL.'x.png"></td>';
				} elseif ($field[$i][$j] == 'o'){
					$value = '<td><img src="'.APP_URL.'o.png"></td>';
				} else {
					if ($is_move) {
						$value = sprintf('<td style="cursor:pointer" onclick="Obj.move(%s, %s);send()"></td>', $i, $j);	
					} else {
						$value = '<td></td>';
					}
					
				}
				$html .= $value;
			}
			$html .= '</tr>';
		}
		$field_tpl = file_get_contents(APP_PATH.'field.tpl');
		$this->html = str_replace('%field%', $html, $field_tpl);
	}

	//	Вывести HTML
	public function showHTML()
	{
		echo $this->html;
		$this->html = '';
	}

	// public function loadSearchMessage(){
	// 	$this->html = "Searching... Please wait";
	// }

	//	Сообщение, которое выводит каждому его состояние в игре и кнопку заново
	public function endMessage($end_array)
	{	
		$p_tag = '<p>%s</p>';
		$win_messages = array(1 => 'Вы восхитительны!', 'Вы выиграли', 'Ура-ура!');
		$lose_messages = array(1 => 'Вы проиграли', 'Ты сражался как зверь!', 'И не стыдно?');

		if($end_array['status'] == 3){
			$this->html .= sprintf($p_tag, 'Ничья');
		} elseif ($end_array['status'] == 1){
			$this->html .= sprintf($p_tag, $win_messages[rand(1, count($win_messages))]);
		} else {
			$this->html .= sprintf($p_tag, $lose_messages[rand(1, count($lose_messages))]);
		}
		
		$this->html .= '<button onclick="closeConnection()">Еще раз</button>';
	}

	//	Функция отправляющая указание клиенту повторить запрос через указанный интервал
	public function repeatRequest($interval, $data)
	{
		header('Repeat-Request: '.$interval);
		header('Data-Request: '.$data);
	}

	//	Функция выясняющая, делать ли клетки на поле активными для последующего выбора хода
	public function isWait($is_move)
	{
		return $is_move ? true : false;
	}
}
?>
