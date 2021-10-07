<?php
if(!defined('IN_CMS')) exit;

class PerfPlaybill extends PerformancesDB {
    const TABLE = '`perf_playbill`';
    const TABLE_STRUCT = ['id', 'url', 'date', 'perf_main_id'];
    public $id, $url, $date, $perf_main_id;

    public function __construct($data = false) {
        parent::__construct($data);

        return $this;
    }

    public function updateFormatDate() {
        if(is_numeric($this->date))
            $this->date = date('d.m.Y H:i:s',$this->date);

        return $this;
    }
}