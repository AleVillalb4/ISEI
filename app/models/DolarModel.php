<?php
namespace app\models;
use \DataBase;
use \Model;

class DolarModel extends Model
{
	protected $table = "dolar";
	protected $primaryKey = "id_dolar";

	public static function getDolar(){
		$model = new static();
		$sql = "SELECT dolar FROM dolar ORDER BY id_dolar DESC LIMIT 1";

		$result = DataBase::getRecord($sql);

		return $result->dolar;
	}

}
