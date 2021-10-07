<?php
/**
 * Created by PhpStorm.
 * User: avalahanovich
 * Date: 21.01.2020
 * Time: 16:53
 */

class PerformancesDB extends PerformancesRecord {

	public function __construct($data = false) {

        parent::__construct(PERFORMANCES_DSN, PERFORMANCES_DB_USER, PERFORMANCES_DB_PASS);

		if($data && (is_array($data) || is_object($data))) {
            foreach ($data as $key => $value) {
                $this->$key = $value;
            }
        }
    }

    /* Инициализация объекта по данным из БД */
    public function initFromDB($find = []) {
        if(empty($find)) return $this;
        $search = $this->findFrom(join('=? AND ', array_keys($find)) . '=?', array_values($find), $this->getTable());
        if(!(bool)$search) return $this;
        foreach ($search as $item) {
            foreach ($item as $key => $value) {
                $this->$key = $value;
            }
        }

        return $this;
    }

	/**
	 * $data - массив, где:
	 *                  ключ - ячейка
	 *                  значение - значение
	 * Если необходим поиск значений, которые не равны,
	 * а к примеру: >, <
	 * Использовать $where совместно с $params
	 * Смотреть подробнее в классе работы с БД -> generateWhere
	 * При этом, возможно использовать одновременно, как $data
	 * так и $where
	 *
	 * @param array         $data
	 * @param array         $where
	 * @param array         $params
	 * @param bool          $initialize
     * @param bool          $init
     * @param bool|string   $method
	 *
	 * @return array
	 */
    public function search($data = [], $where = [], $params = [], $initialize = true, $keyBy = false, $select = '*', $init = false, $method = false) {
        if(empty($data) && empty($where)) return [];

        $where = $this->generateWhere($where, $params);
        if(isset($where['where']) && !empty($data)) $where['where'] .= ' AND ';
        $params = isset($where['params']) ? array_merge(array_values($data), $where['params']) : array_values($data);
        $find = $this->findFrom((isset($where['where']) ? $where['where'] : "") . (empty($data) ? '' : join('=? AND ', array_keys($data)) . '=?'), $params, $this->getTable(), $select,PDO::FETCH_ASSOC);
        if(!$initialize || !(bool)$find) return [];

        $class = get_called_class();
        $result = [];
        foreach($find as $item) {
            $tmp = new $class();
	        $tmp->setData($item);
            if($init && method_exists($tmp, 'init'))
                $tmp->init();
            if($method !== false && method_exists($tmp, $method))
                $tmp->$method();
	        if(!$keyBy)
                 $result[] = $tmp;
	        else $result[$tmp->$keyBy] = $tmp;
        }

        return $result;
    }

    public function getTable() {
    	$class = get_called_class();

        return $class::TABLE;
    }

    public function getTableStruct() {
        $class = get_called_class();

        return $class::TABLE_STRUCT;
    }

    /* Установка свойст объекта */
    public function setData($data = []) {
        if(!empty($data))
	        foreach ($data as $key => $item) {
	            $this->$key = $item;
	        }

        return $this;
    }

    public function save() {
        $data = get_object_vars($this);
        foreach ($data as $key => $val) {
            if(!in_array($key, $this->getTableStruct()))
                unset($data[$key]);
        }

        unset($data['id']);
        if (isset($this->id) && !empty($this->id) && $this->id != NULL) {
            $res = $this->update($data, 'id=?', array($this->id), $this->getTable());
        } else
            $res = $this->insert($data, $this->getTable());

        return $res;
    }

    public function del() {
        if(!$this->id) return false;

        return $this->delete('id=?', array($this->id), $this->getTable());
    }

	public function owner() {
		if(!isset($this->id)) $this->owner = [];
		else {
			$find = $this->search(['id' => $this->owner_id], [], [], true, $this->getKeyBy());
			if(!(bool)$find) $this->owner = [];
			else foreach($find as $key => $owner) {
				$owner->owner();
				$this->owner = $owner;
			}
		}

		return $this;
	}

	public function children() {
		if(!isset($this->id)) $this->children = [];
		else {
			$find = $this->search(['owner_id' => $this->id], [], [], true, $this->getKeyBy());
			if(!(bool)$find) $this->children = [];
			else foreach($find as $key => $child) {
				$child->children();
				$this->children[$key] = $child;
			}
		}

		return $this;
	}

    public function getFileUpload($class = false) {
        if($class === false)
            $class = get_called_class();
        $data = [];
        foreach ($class::TABLE_STRUCT as $val) {
            if(property_exists($class, $val))
                $data["{{$val}}"] = $this->{$val};
        }

        return $data;
    }

    public function getUrlDirFiles($dir, $files) {
        $res = [];
        foreach ($files as $file) {
            $res[] = $dir . DS . $file;
        }

        return $res;
    }

    public function delDir() {
        $class = get_called_class();
        if($class::DIRDEL) {
            foreach ($class::DIRDEL as $key => $dir) {
                $r = $this->getFileUpload($class);
                $dir = str_replace(array_keys($r), array_values($r), $dir);
                if(is_dir($dir)) {
                    $this->delTree($dir);
                }
            }
        }

        return $this;
    }

    public function delTree($dir) {
        $files = array_slice(scandir($dir), 2);
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->delTree("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }

    public function getFiles($type = false) {
        $class = get_called_class();
        if($class::FILEUPLOAD) {
            if ($type === false)
                foreach ($class::FILEUPLOAD as $key => $dir) {
                    $r = $this->getFileUpload($class);
                    $dir = str_replace(array_keys($r), array_values($r), $dir);
                    $url_dir = str_replace(CMS_ROOT, "", $dir);
                    if(is_dir($dir))
                        $this->files[$key] = $this->getUrlDirFiles($url_dir, array_slice(scandir($dir), 2));
                }
            else if (isset($class::FILEUPLOAD[$type])) $this->files[$type] = array_slice(scandir($class::FILEUPLOAD[$type]), 2);
        }

        return $this;
    }
}