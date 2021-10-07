<?php
if(!defined('IN_CMS')) exit;

class PerformancesRecord {
    const PREF_MSG      = '====================';
    private $__CONN__   = false;
    private $debug      = false;
    private $type       = 'var_dump';
    private $keyBy      = 'id';
	
	/**
	 * Подключение к БД
	 *
	 * @param $DB_DSN
	 * @param $DB_USER
	 * @param $DB_PASS
	 */
    public function __construct($DB_DSN, $DB_USER, $DB_PASS) {
        if(empty($DB_DSN))
            return false;

        $type_db = explode(':', $DB_DSN, 2);

        if(PerformancesDBConnect::$__CONN__)
            return true;

        try {
            PerformancesDBConnect::$__CONN__ = new PDO($DB_DSN, $DB_USER, $DB_PASS);
            if(!PerformancesDBConnect::$__CONN__)
                return false;

            if($type_db[0] == 'mysql')
                PerformancesDBConnect::$__CONN__->exec('SET NAMES \'UTF8\'');
            return true;
        } catch (PDOException $e) {
            //throw new Exception('Ошибка подключения к БД [' . $type_db .'] (плагин: Vote), проверьте настройки плагина' . PHP_EOL . $_SERVER['SERVER_NAME'] . '::' . $e->getMessage());
            return false;
        }
    }
	
	/**
	 * Функция задания ключа вывода
	 */
	public function setKeyBy($key) {
		$this->keyBy = $key;
		
		return $this;
	}
	
	/**
	 * Функция вывода ключа
	 * @return string
	 */
	public function getKeyBy() {
		return $this->keyBy;
	}
	
	/**
	 * Функция создания базы данных
	 *
	 * @param bool   $table
	 * @param string $set
	 * @param string $collate
	 *
	 * @return false|PDOStatement
	 */
    public function createTable($table = false, $set = 'cp1251', $collate = 'cp1251_general_ci') {
        if(!$table)
            return false;
        if(!PerformancesDBConnect::$__CONN__)
            return false;
        return PerformancesDBConnect::$__CONN__->query('CREATE DATABASE IF NOT EXISTS `' . $table . '` DEFAULT CHARACTER SET ' . $set . ' COLLATE ' . $collate . '; USE `' . $table . '`;');
    }
	
	/**
	 * Функция включения / отключения debug режима
	 * Вывод через var_dump любых sql запросов
	 *
	 * @param bool   $status
	 * @param string $type
	 */
    public function setDebug($status = false, $type = 'var_dump') {
        $this->debug    = $status;
        $this->type     = $type;
    }

    public function showDebug($method) {
        if(!$this->debug) return;
        $arg = func_get_args();
        unset($arg[0]);
        switch($this->type) {
	        default: {var_dump(self::PREF_MSG . $method . self::PREF_MSG, $arg);}; break;
        }
    }

    /**
     * Функция проверки подключения
     */
    public function checkConnect() {
        if(!PerformancesDBConnect::$__CONN__)
            return false;
        return true;
    }
	
	/**
	 * Функция для отображение таблицы, в заданом классе
	 *
	 * @param $className
	 *
	 * @return false|mixed
	 */
    private function tableNameFromClass($className) {
        if(!class_exists($className) || !defined($className . '::TABLE_NAME'))
            return false;

        $this->showDebug('tableNameFromClass', constant($className . '::TABLE_NAME'));
        return constant($className . '::TABLE_NAME');
    }
	
	/**
	 * Функция поиска в базе данных
	 * Принимает 4 параметра:
	 * $where - По каким параметрам будет происходить поиск, пример: ('type=? AND client=? ORDER BY ID DESC LIMIT 1')
	 * $params - Параметры, по которым будет происходить поиск
	 * $table_name - Таблица, в которой будет производиться поиск
	 *      Если не задана, просмотрит константу ::TABLE_NAME из вызванного класа
	 * $select - по дефолту: * (поиск по всему), так же можно явно задать выводимые стобцы
	 *
	 * @param string $where
	 * @param array  $params
	 * @param bool   $table_name
	 * @param string $select
	 * @param int    $fetch_style
	 *
	 * @return array|false
	 */
    public function findFrom($where = '', $params = [], $table_name = false, $select = '*', $fetch_style = PDO::FETCH_OBJ) {
        $table_name = $table_name ? $table_name        : $this->tableNameFromClass(get_called_class());
        $where = !empty($where)   ? ' WHERE ' . $where : '';

        $this->showDebug('findFrom', 'prepare', "SELECT " . $select . ' FROM ' . $table_name . $where, 'execute:', $params);
        $res = PerformancesDBConnect::$__CONN__->prepare("SELECT " . $select . ' FROM ' . $table_name . $where);
        $exs = $res->execute($params);
        if(!$exs) {
            var_dump('ERROR FIND FROM!');
            var_dump(PerformancesDBConnect::$__CONN__->errorInfo());
            exit();
        }
        $row = $res->fetchAll($fetch_style);
        if(!$row)
            return false;
        return $row;
    }
	
	/**
	 * Функция insert в БД
	 * Принимает 2 параметра:
	 * $data - массив данных, пример: (array('name' => 'mysql_host', 'value' => 'localhost'))
	 * $table_name - Таблица, в которой будет производиться поиск
	 *      Если не задана, просмотрит константу ::TABLE_NAME из вызванного класа
	 *
	 * @param array $data
	 * @param bool  $table_name
	 *
	 * @return bool
	 */
    public function insert($data = array(), $table_name = false) {
        if(empty($data))
            return false;
        $table_name = $table_name ? $table_name : $this->tableNameFromClass(get_called_class());
        $tmp = array();
        foreach ($data as $key => $value) {
            $tmp[':' . $key] = $value;
        }
        $this->showDebug('insert', 'prepare:', 'INSERT INTO ' . $table_name . ' (' . join(',', array_keys($data)) . ') VALUES (' . join(',', array_keys($tmp)) . ')', 'execute:', $tmp);
        $res = PerformancesDBConnect::$__CONN__->prepare('INSERT INTO ' . $table_name . ' (`' . join('`, `', array_keys($data)) . '`) VALUES (' . join(',', array_keys($tmp)) . ')');
        return $res->execute($tmp);
    }
	
	/**
	 * Функция обновления данных в базе данных
	 * Принимает 4 параметра:
	 * $data - массив данных, пример: (array('name' => 'mysql_host', 'value' => 'localhost'))
	 * $where - По каким параметрам будет происходить замена, пример: ('type=? AND client=?')
	 * $params - Значения, по которым будет происходить замена
	 * $table_name - Таблица, в которой будет производиться поиск
	 *      Если не задана, просмотрит константу ::TABLE_NAME из вызванного класса
	 *
	 * @param array  $data
	 * @param string $where
	 * @param array  $params
	 * @param bool   $table_name
	 *
	 * @return bool
	 */
    public function update($data = array(), $where = '', $params = array(), $table_name = false) {
        if(empty($data))
            return false;
        $table_name = $table_name ? $table_name        : $this->tableNameFromClass(get_called_class());
        $where = !empty($where)   ? ' WHERE ' . $where : '';
        $tmp = array();
        foreach ($data as $key => $value) {
            $tmp[] = '`' . $key . '`=' . PerformancesDBConnect::$__CONN__->quote($value);
        }
        $this->showDebug('update', 'prepare:', 'UPDATE ' . $table_name . ' SET ' . join(',', $tmp) . $where, 'execute:', $params);
        $res = PerformancesDBConnect::$__CONN__->prepare('UPDATE ' . $table_name . ' SET ' . join(',', $tmp) . $where);
        return $res->execute($params);
    }
	
	/**
	 * Функция добавления данных в базу данных
	 * Принимает 2 параметра:
	 * $data - двумерный массив данных, пример: (array('name' => array('test', 'qwerty'), 'value' => array('1', '2'))
	 * $table_name - таблица, в которую будет произведена запись
	 *      Есле не задана, просмотрит константу ::TABLE_NAME из вызванного класса
	 *
	 * @param array $data
	 * @param bool  $table_name
	 *
	 * @return bool
	 */
    public function insertArr($data = array(), $table_name = false) {
        if(empty($data))
            return false;
        $table_name = $table_name ? $table_name : $this->tableNameFromClass(get_called_class());
        $tmp = array();
        $count = count($data[array_keys($data)[0]]);
        for($i = 0; $i < $count; $i++) {
            $tmp[] = '(' . $this->generateInsertVal($data, $i) . ')';
        }
        $this->showDebug('insertArr', 'prepare:', 'INSERT INTO ' . $table_name . ' (`' . join('`, `', array_keys($data)) . '`) VALUES ' . join(',', $tmp));
        $res = PerformancesDBConnect::$__CONN__->prepare('INSERT INTO ' . $table_name . ' (`' . join('`, `', array_keys($data)) . '`) VALUES ' . join(',', $tmp));
        return $res->execute();
    }
	
	/**
	 * Вспомогательная функция для формирования сложных запросов,
	 * Используется в функциях с приставкой Arr, внутри класса FeedbacksRecord
	 *
	 * Использование из вне, не допустимо!
	 *
	 * @param $data
	 * @param $i
	 *
	 * @return string
	 */
    private function generateInsertVal($data, $i) {
        $tmp = array();
        foreach (array_keys($data) as $val) {
            $tmp[] = PerformancesDBConnect::$__CONN__->quote($data[$val][$i]);
        }
        $this->showDebug('generateInsertVal', join(',', $tmp));
        return join(',', $tmp);
    }
	
	/**
	 * Вспомогательная функция для формирования сложных запросов,
	 * Используется в функциях с приставкой Arr, внутри класса FeedbacksRecord
	 *
	 * Использование из вне, не допустимо!
	 *
	 * @param array  $data
	 * @param string $glue
	 *
	 * @return string
	 */
    private function joinQuote($data = array(), $glue = ',') {
        $this->showDebug('joinQuote', join($glue, array_map(array(PerformancesDBConnect::$__CONN__, 'quote'), $data)));
        return join($glue, array_map(array(PerformancesDBConnect::$__CONN__, 'quote'), $data));
    }
	
	/**
	 * Функция обновления нескольких строк данных в бд, одним запросом
	 *
	 * @param array $data
	 * @param array $where
	 * @param bool  $table_name
	 *
	 * @return bool
	 */
    public function updateArr($data = array(), $where = array(), $table_name = false) {
        if(empty($data) || empty($where) || count($where[array_keys($where)[0]]) != count($data[array_keys($data)[0]]))
            return false;

        $table_name = $table_name ? $table_name : $this->tableNameFromClass(get_called_class());
        $wher = $where ? ' WHERE ' . join(' AND ', $this->generateWhereArr($where)) : '';
        $tmp = array();
        foreach (array_keys($data) as $value) {
            if(!is_array($data[$value]))
                continue;
            $tmp[] = '`' . $value . '`=' . $this->generateCaseUpdate($data[$value], $where);
        }
        $this->showDebug('updateArr', 'prepare:', 'UPDATE ' . $table_name . ' SET ' . join(', ', $tmp) . $wher, 'execute:', $this->generatePrepare($where, count($where[array_keys($where)[0]]), count(array_keys($data))));
        $res = PerformancesDBConnect::$__CONN__->prepare('UPDATE ' . $table_name . ' SET ' . join(', ', $tmp) . $wher);
        return $res->execute($this->generatePrepare($where, count($where[array_keys($where)[0]]), count(array_keys($data))));
    }
	
	/**
	 * Вспомогательная функция для формирования сложных запросов,
	 * Используется в функциях с приставкой Arr, внутри класса FeedbacksRecord
	 *
	 * Использование из вне, не допустимо!
	 *
	 * @param $where
	 * @param $count
	 * @param $matr
	 *
	 * @return array
	 */
    private function generatePrepare($where, $count, $matr) {
        $tmp = array();
        for($z = 0; $z < $matr; $z++) {
            for ($i = 0; $i < $count; $i++) {
                foreach (array_keys($where) as $item) {
                    if (isset($where[$item][$i]))
                        $tmp[] = $where[$item][$i];
                    else
                        $tmp[] = $where[$item][0];
                }
            }
        }
        $this->showDebug('generatePrepare', 'return:', $tmp);
        return $tmp;
    }
	
	/**
	 * Вспомогательная функция для формирования сложных запросов,
	 * Используется в функциях с приставкой Arr, внутри класса FeedbacksRecord
	 *
	 * Использование из вне, не допустимо!
	 *
	 * @param $where
	 *
	 * @return array
	 */
    private function generateWhereArr($where) {
        $tmp = array();
        foreach ($where as $key => $val) {
            $tmp[] = $key . ' in (' . $this->joinQuote($val) . ')';
        }
        $this->showDebug('generateWhereArr', 'return:', $tmp);
        return $tmp;
    }
	
	/**
	 * Вспомогательная функция для формирования сложных запросов,
	 * Используется в функциях с приставкой Arr, внутри класса FeedbacksRecord
	 *
	 * Использование из вне, не допустимо!
	 *
	 * @param array $data
	 * @param array $where
	 *
	 * @return false|string
	 */
    private function generateCaseUpdate($data = array(), $where = array()) {
        if(empty($data) || empty($where))
            return false;
        $tmp = '(case';
        foreach ($data as $key => $value) {
            $tmp_where = array();
            foreach (array_keys($where) as $item) {
                $tmp_where[] = $item . '=?';
            }
            $tmp .= ' when ' . join(' AND ', $tmp_where) . ' then ' . PerformancesDBConnect::$__CONN__->quote($value);
        }
        $tmp .= ' end)';
        $this->showDebug('generateCaseUpdate', 'return:', $tmp);
        return $tmp;
    }
	
	/**
	 * Функция удаления данных из базы данных
	 * Принимает 3 параметра:
	 * $where - По каким параметрам будет происходить удаление, пример: ('type=? AND client=?')
	 * $params - Значения, по которым будет происходить удаление
	 * $table_name - Таблица, в которой будет производиться поиск
	 *      Если не задана, просмотрит константу ::TABLE_NAME из вызванного класа
	 *
	 * @param string $where
	 * @param array  $params
	 * @param bool   $table_name
	 *
	 * @return bool
	 */
    public function delete($where = '', $params = array(), $table_name = false) {
        $table_name = $table_name ? $table_name        : $this->tableNameFromClass(get_called_class());
        $where = !empty($where)   ? ' WHERE ' . $where : '';

        $this->showDebug('generateCaseUpdate', 'prepare:', 'DELETE FROM ' . $table_name . $where, 'execute:', $params);
        $res = PerformancesDBConnect::$__CONN__->prepare('DELETE FROM ' . $table_name . $where);
        return $res->execute($params);
    }
	
	/**
	 * Функция запроса
	 * Принимает 2 параметра:
	 * $sql - полный SQL запрос, с ? вместо значений
	 * $values - массив значений, для ?
	 *
	 * @param bool $sql
	 * @param bool $values
	 *
	 * @return array|false|PDOStatement
	 */
    public function query($sql = false, $values = false) {
        if (is_array($values)) {
            if($this->debug)
                $this->showDebug('query', 'prepare:', $sql, 'execute:', $values);
            $stmt = PerformancesDBConnect::$__CONN__->prepare($sql);
            $stmt->execute($values);
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } else {
            return PerformancesDBConnect::$__CONN__->query($sql);
        }
    }
	
	/**
	 * Вспомогательная функция, для доступа из вне
	 *
	 * @param $value
	 *
	 * @return false|string
	 */
    public function quote($value) {
        $this->showDebug('quote', 'quote:', $value);
        return PerformancesDBConnect::$__CONN__->quote($value);
    }
	
	/**
	 * Функция удаления таблиц
	 * Принимает 1 параметр:
	 * $table_name - массив с названием таблиц в БД, которые необходимо удалить
	 * пример: array('log', 'request')
	 *
	 * @param array $table_name
	 *
	 * @return bool
	 */
    public function dropTable(Array $table_name) {
        if(empty($table_name))
            return false;
        $del = array();
        foreach($table_name as $item) {
            $del[] = '`' . $item . '`';
        }
        $this->showDebug('dropTable', 'prepare:', 'DROP TABLE ' . join(', ', $del));
        $res = PerformancesDBConnect::$__CONN__->prepare('DROP TABLE ' . join(', ', $del));
        return $res->execute();
    }
	
	/**
	 * type: 0 - date format, 1 - realUnixTime, 2 - Конвертер из даты в unix время
	 * Если $type == 2, а $unix == 1, то достаточно поместить всю дату в $day
	 * Если $type == 2, а $unix == 0, то необходимо поместить день в $data['day'], месяц в месяц и т.д.
     * 01.01.2021 19:15
	 *
	 * @param int    $type
	 * @param int    $unix
	 * @param string $format
	 * @param array  $data
	 *
	 * @return false|int|string
	 */
    public function iTime($type = 0, $unix = 0, $format = 'd.m.Y H:i:s', $data = array()) {
        switch ($type) {
            case '0': {if($unix) return date($format, $unix);}; break;
            case '1': {return strtotime(date($format));}; break;
            case '2': {
                if($unix == 1) {
                    $mh = substr($data['day'], 3, 2);
                    $ye = substr($data['day'], 6, 4);
                    $day = substr($data['day'], 0, 2);

                    $data['hour'] = strlen($data['day']) > 11 ? substr($data['day'], 11, 2) : '00';
                    $data['min'] = strlen($data['day']) > 14 ? substr($data['day'], 14, 2) : '00';
                    $data['sec'] = strlen($data['day']) > 17 ? substr($data['day'], 17, 2) : '00';
                }
                return mktime($data['hour'], $data['min'], $data['sec'], $mh, $day, $ye);
            }; break;
            default: return time();
        }
    }
	
	/**
	 * Функция для формирования $where со значениями: >, < и т.д.
	 *
	 * Пример: $data = array('id' => '23', 'date' => '700')
	 *          Ключ        - ячейка в бд
	 *          Значение    - значение
	 *         $params = array('=', '>')
	 * Сформирует: id=? AND date>?
	 * Оба массива обязательно должны иметь одинаковый размер
	 * по значениям
	 *
	 * Возвращает массив: where & params
	 * Для запроса: findFrom(&where, &params...)
	 *
	 * @param array $data
	 * @param array $params
	 *
	 * @return array|false
	 */
    public function generateWhere($data = array(), $params = array()) {
        if(empty($data) || empty($params) || count(array_values($data)) !== count(array_values($params))) return false;
        $result = array('where' => '', 'params' => array_values($data));
        for($i = 0; $i < count(array_values($data)); $i++) {
            $result['where'] .= ($i > 0 ? ' AND ' : '') . array_keys($data)[$i] . $params[$i] . '?';
        }
        return $result;
    }

    public function lastInsertId() {
        return PerformancesDBConnect::$__CONN__->lastInsertId();
    }
}