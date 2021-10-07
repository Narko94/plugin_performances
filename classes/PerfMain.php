<?php
if(!defined('IN_CMS')) exit;

class PerfMain extends PerformancesDB {
    const TABLE = '`perf_main`';
    const TABLE_STRUCT = ['id', 'title', 'description', 'type', 'premiere', 'rating', 'perf_page_id'];
    const FILEUPLOAD = [
        "perf_main"         => PERFORMANCES_PUBLIC . "/{perf_page_id}/perf_main/{id}/main",
        "perf_main_mob"     => PERFORMANCES_PUBLIC . "/{perf_page_id}/perf_main/{id}/mobile",
        "perf_poster"       => PERFORMANCES_PUBLIC . "/{perf_page_id}/perf_main/{id}/poster"
    ];
    const DIRDEL = [
        PERFORMANCES_PUBLIC . "/{perf_page_id}"
    ];
    public $id, $title, $description, $type, $premiere, $rating, $perf_page_id, $perf_playbill;

    public function __construct($data = false) {
        parent::__construct($data);

        return $this;
    }

    public function init() {
        $this->perf_playbill = (new PerfPlaybill())->search(['perf_main_id' => $this->id]);

        return $this;
    }

    public function del() {
        parent::del();

        $this->delDir();

        if(count($this->perf_playbill))
            foreach ($this->perf_playbill as $playbill) {
                $playbill->del();
            }
    }
}