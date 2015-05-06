<?php 
/**
* 	Класс для проверки победы/поражения в игре
*/
class WinLines
{
	private $field;
	function __construct($field)
	{
		$this->field = $field;
	}

	public function checkWin()
	{
		foreach ($this->lines() as $line) {
			$o = $x = 0;
			foreach ($line as $cell) {
				if($this->field[$cell[0]][$cell[1]] == 'x'){
					$x++;
				} elseif ($this->field[$cell[0]][$cell[1]] == 'o') {
					$o++;
				}
			}

			if($x == 3){
				return array('line' => $line, 'winner' => 'x');
			} elseif($o == 3){
				return array('line' => $line, 'winner' => 'o');
			}
		}
		return false;
	}

	public function lines()
	{
		return array(
			array('11','12','13'),
			array('21','22','23'),
			array('31','32','33'),
			array('11','21','31'),
			array('12','22','32'),
			array('13','23','33'),
			array('11','22','33'),
			array('13','22','31')
			);
	}
}
