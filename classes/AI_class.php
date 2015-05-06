<?php
/*
*	Класс для расчета хода ИИ
*/
class AIclass
{
	public $field;
	private $moves;
	public $type;
	public $enemy_type;
	private $difficulty;
	private $lines = array(
				array('11','12','13'),
				array('21','22','23'),
				array('31','32','33'),
				array('11','21','31'),
				array('12','22','32'),
				array('13','23','33'),
				array('11','22','33'),
				array('13','22','31')
			);

	function __construct($game)
	{
		$this->field = $game['field'];
		$this->difficulty = $game['difficulty'];
		$this->_getType();
	}

	private function _getType()
	{	
		$counter = $this->countMoves($this->field);
		
		if ($counter%2) {
			$this->type = 'o';
			$this->enemy_type = 'x';
		} else {
			$this->type = 'x';
			$this->enemy_type = 'o';
		}
	}

	private function getFreeCells($field)
	{
		$free_cells = array();

		for ($i=1; $i <= 3; $i++) { 
			for ($j=1; $j <= 3; $j++) {
				if (empty($field[$i][$j])) {
					$free_cells[$i][$j] = $field[$i][$j];			
				}
			}
		}
		return $free_cells;
	}

	public function getField()
	{
		return $this->field;
	}

	public function checkWinMove($field)
	{
		for ($i=1; $i <= 3; $i++) { 
			for ($j=1; $j <= 3; $j++) { 
				if (empty($field[$i][$j])) {
					$field2 = $this->writeMove($field, $i, $j);

					foreach ($this->lines as $line) {
						$win_line = 0;
						foreach ($line as $cell) {
							if($field2[$cell[0]][$cell[1]] == $this->type){
								$win_line++;
							}
						}

						if($win_line == 3){
							return array('i' => $i, 'j' => $j);
						}
					}
				}
			}
		}

		return false;
	}

	public function checkWinEnemyNextMove($field)
	{
		for ($i=1; $i <= 3; $i++) { 
			for ($j=1; $j <= 3; $j++) { 
				if (empty($field[$i][$j])) {
					$field2 = $this->writeMove($field, $i, $j, false);

					foreach ($this->lines as $line) {
						$win_line = 0;
						foreach ($line as $cell) {
							if($field2[$cell[0]][$cell[1]] != $this->type && !empty($field2[$cell[0]][$cell[1]])){
								$win_line++;
							}
						}

						if($win_line == 3){
							return array('i' => $i, 'j' => $j);
						}
					}
				}
			}
		}

		return false;
	}
	public function writeRandomMove($field)
	{
		$free_cells = $this->getFreeCells($field);

		while (true){
			$i = rand(1,3);
			$j = rand(1,3);

			if (isset($free_cells[$i][$j])) {
				return $this->writeMove($field, $i, $j);
			}
		}

	}
	public function generateMove($field)
	{
		//----RAGEEEEEE---
		$old_field = $field;
		//----------------

		switch ($this->difficulty) {
			case '1':
				$field = $this->writeRandomMove($field);
				break;
			case '2':

				if (($cond = $this->checkWinMove($field)) !== false) {
					$field = $this->writeMove($field, $cond['i'], $cond['j']);
				} elseif (($cond = $this->checkWinEnemyNextMove($field)) !== false){
					$field = $this->writeMove($field, $cond['i'], $cond['j']);
				} else {
					$field = $this->writeRandomMove($field);
				}

				break;
			case '3':

				include $_SERVER['DOCUMENT_ROOT'].APP_URL.'patterns.php';

				$is_pattern = false;
				foreach ($patterns as $val) {
					if (($cond = $this->findPattern($field, $val)) !== false) {
						$field = $this->writePatternMove($field, $cond);
						$is_pattern = true;
					}
				}

				if (!$is_pattern) {
					if (($cond = $this->checkWinMove($field)) !== false) {
						$field = $this->writeMove($field, $cond['i'], $cond['j']);
					} elseif (($cond = $this->checkWinEnemyNextMove($field)) !== false){
						$field = $this->writeMove($field, $cond['i'], $cond['j']);
					} else {
						$field = $this->writeRandomMove($field);
					}
				}

				break;
			default:
				throw new Exception('Choose your destiny');
				break;
		}		

		//----RAGEEEEEE---
		for ($i=1; $i <= 3; $i++) { 
			for ($j=1; $j <= 3; $j++) { 
				if ($old_field[$i][$j] != $field[$i][$j]) {
					return array('i' => $i, 'j' => $j);
				}
			}
		}
		//----------------

		return $field;
	}

	public function countMoves($field)
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
	public function writeMove($field, $i, $j, $type = true)
	{
		$field[$i][$j] = $type ? $this->type :  $this->enemy_type;
		
		return $field;
	}

	public function writePatternMove($field, $pattern)
	{
		for ($i=1; $i <= 3; $i++) { 
			for ($j=1; $j <= 3; $j++) { 
				if($field[$i][$j] != $pattern[$this->countMoves($field)+1][$i][$j]){
					return $field = $this->writeMove($field, $i, $j);
				}
			}
		}
	}

	function findPattern($field, $pattern)
	{
		for ($i=1; $i <= 4; $i++) { 
			if($this->checkPattern($field, $pattern)){
				return $pattern;
			} else {
				foreach ($pattern as &$path) {
					$path = $this->rotate($path);
				}
			}
		}

		foreach ($pattern as &$path) {
			$path = $this->flip($path, 'v');
		}

		for ($i=1; $i <= 4; $i++) { 
			if($this->checkPattern($field, $pattern)){
				return $pattern;
			} else {
				foreach ($pattern as &$path) {
					$path = $this->flip($path, 'h');
					$path = $this->rotate($path);
					$path = $this->flip($path, 'h');
				}
			}
		}

		return false;
	}

	public function checkPattern($field, $pattern)
	{
		$cond = true;

		if (!isset($pattern[$this->countMoves($field)])) {
			return false;
		}

		for ($i=1; $i <= 3; $i++) { 
			for ($j=1; $j <= 3; $j++) { 
				if($field[$i][$j] != $pattern[$this->countMoves($field)][$i][$j]){
					$cond = false;
				}
			}
		}

		return $cond;
	}

	public function flip($field, $str)
	{
		$arr = $field;
		$change_type = 'v';	//	vertical or h - horizontal

		$arr[1][1] = $str == $change_type ? $field[1][3] : $field[3][1];
		$arr[1][3] = $str == $change_type ? $field[1][1] : $field[3][3];
		$arr[3][1] = $str == $change_type ? $field[3][3] : $field[1][1];
		$arr[3][3] = $str == $change_type ? $field[3][1] : $field[1][3];

		if ($str == $change_type) {
			$arr[2][1] = $field[2][3];
			$arr[2][3] = $field[2][1];
		} else {
			$arr[1][2] = $field[3][2];
			$arr[3][2] = $field[1][2];
		}

		return $arr;
	}

	public function diagonalFlip($field, $str)
	{
		$arr = $field;
		$change_type = 's';	//	slash or b - backslash

		$arr[1][2] = $str == $change_type ? $field[2][3] : $field[2][1];
		$arr[2][1] = $str == $change_type ? $field[3][2] : $field[1][2];
		$arr[3][2] = $str == $change_type ? $field[2][1] : $field[2][3];
		$arr[2][3] = $str == $change_type ? $field[1][2] : $field[3][2];

		if ($str == $change_type) {
			$arr[1][1] = $field[3][3];
			$arr[3][3] = $field[1][1];
		} else {
			$arr[1][3] = $field[3][1];
			$arr[3][1] = $field[1][3];
		}

		return $arr;
	}

	public function rotate($field)
	{
		// rotate only right

		$arr = $field;

		$arr[1][1] = $field[3][1];
		$arr[1][2] = $field[2][1];
		$arr[1][3] = $field[1][1];
		$arr[2][1] = $field[3][2];
		$arr[2][3] = $field[1][2];
		$arr[3][1] = $field[3][3];
		$arr[3][2] = $field[2][3];
		$arr[3][3] = $field[1][3];

		return $arr;
	}

}
?>
