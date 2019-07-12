<?php
class db{
	public $pdo;
	public $table;
	public $where;
	public $data;
	public $limit;
	public $search;
	function __construct($location,$username,$userpassword,$dbname){
		$this->pdo = new PDO("mysql:host=$location;dbname=$dbname","$username","$userpassword");
		//连接到数据库
	}
	function table($table){
		$this->table=$table.' ';
		return $this;
		// 返回当前数据表名
	}
	function where($where){
		if(empty($where)){
			return $this;
			// 如果where判断的条件为空，则返回空
		}
		//where 字段1 = 值1 and 字段2 = 值2;条件的语句
		$str = ' where ';
		//拼接 where
		if(is_array($where)){
			foreach ($where as $key => $value) {
				// $key = 字段  $value = 值
				$str .= $key.'="'.$value.'" and ';
			}
			//where 字段1="值1" and 字段2="值2" and
			$str = rtrim($str,' and ');
			//去除多余字符 where 字段1="值1" and 字段2="值2"
		}else{
			$str .= $where;
			//如果是字符串 则拼接上 然后返回
		}
		$this->where = $str;
		return $this;
	}
	function find($a){
		$data = $this->data;
		$keys = array_keys($data[0]);
		$adata = [];
		if(empty($a)){
			return $this;
		}
		foreach ($keys as $key => $value) {
			if($value==$a){
				$adata[] = $data[0][$value];
			}
		}
		// var_dump($adata);
		$this->data = $adata;
		return $this->data;
		// find('里面填写主要查找的字段')
		// if(empty($a)){
		// 	// 如果限制为空则不限制
		// 	return $this->pdo->query('select * from '.$this->table.$this->where)->fetch(PDO::FETCH_ASSOC);
		// }
		// // 如果有则显示
		// echo 'select '.$a.' from '.$this->table.$this->where;
		// return $this->pdo->query('select '.$a.' from '.$this->table.$this->where)->fetch(PDO::FETCH_ASSOC);
	}
	function search($data){
		if(empty($data)){
			return $this;
		}
		$str = '';
		foreach ($data as $key => $value) {
			$str .= ' and '.$key.' like '.'\'%'.$value.'%\'';
		}
		if(!empty($this->where)){
			$str = $this->where.$str;
		}
		$this->where = ltrim($str,'and ');
		echo "$this->where";
		return $this;
	}
	function select(){
		// 显示全部数据
		echo 'select * from '.$this->table.$this->where.$this->limit;
		$this->data = $this->pdo->query('select * from '.$this->table.$this->where.$this->limit)->fetchAll(PDO::FETCH_ASSOC);
		return $this;
	}
	function limit($begin,$count){
		$this->limit = ' limit '.$begin.','.$count;
		return $this;
	}
	function delete(){
		// 删除表中数据
		if(empty($this->where)){
			$sql = 'delete from '.$this->table;
			//删除表中所有数据
		}else{
			$sql = 'delete from '.$this->table.$this->where;
			//删除表中限制的数据
		}
		$this->pdo->exec($sql);
		// 操作
	}
	function add($data){
		if(empty($data)){
			return '没有要添加的数据';
		}else{
			if(is_array($data)){
				$keys = $values = '';
				foreach ($data as $key => $value) {
					$keys .= $key.',';
					$values .= '"'.$value.'",';
				}
				$keys = rtrim($keys,',');
				$values = rtrim($values,',');
				$sql = "insert into ".$this->table.'('.$keys.')'.' values('.$values.')';
				$this->pdo->exec($sql);
			}
		}
	}
	function update($data){
		$str = '';
		foreach ($data as $key => $value) {
			$str .= $key .'="'.$value.'",';
		}
		$str = rtrim($str,',');
		$sql = 'update '.$this->table.' set '.$str.$this->where;
		$this->pdo->exec($sql);
	}
}
$location = '127.0.0.1';
$dbname = '1610b';
$username = 'root';
$userpassword = 'root';
$db = new db($location,$username,$userpassword,$dbname);
// $a = $db->table('file')->where('file_id = 4')->search(['file_content'=>'dhf'])->limit(0,3)->select();
//模糊查询并加分页
// $a = $db->table('file')->where('file_id=4')->search(['file_content'=>'j'])->limit(0,4)->select()->find('file_content');
//查找单条数据
// $a = $db->table('file')->where('file_id = 4')->limit(0,2)->select();
// 查找全部数据
// $a = $db->table('file')->limit('1','2')->select();
// 分页
// $db->table('file')->add(['file_content'=>'jasdhfjhajhjfasd阿萨德aj']);
// 添加数据
// $db->table('file')->where('file_id = 3')->update(['file_content'=>1]);
// 修改数据
// echo "<pre>";
// var_dump($a);
// 打印数据