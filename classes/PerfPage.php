<?php
if(!defined('IN_CMS')) exit;

class PerfPage extends PerformancesDB {
    const TABLE = '`perf_page`';
    const TABLE_STRUCT = ['id', 'slug', 'title', 'left', 'right', 'video', 'type', 'description', 'position'];
    const FILEUPLOAD = [
        "page_bg"       => PERFORMANCES_PUBLIC . "/{id}/page_bg",
        "page_bg_mob"   => PERFORMANCES_PUBLIC . "/{id}/page_bg_mobile",
        "page_gellary"  => PERFORMANCES_PUBLIC . "/{id}/page_gellary",
        "page_cover"    => PERFORMANCES_PUBLIC . "/{id}/page_cover"
    ];
    const DIRDEL = [
        PERFORMANCES_PUBLIC . "/{id}"
    ];
    public $id, $slug, $title, $left, $right, $video, $type, $perf_main, $description, $position;

    public function __construct($data = false) {
        parent::__construct($data);

        return $this;
    }

    public function init() {
        $this->perf_main = (new PerfMain())->search(['perf_page_id' => $this->id]);

        return $this;
    }

    public function del() {
        parent::del();

        $this->delDir();

        if(count($this->perf_main))
            foreach ($this->perf_main as $main) {
                $main->del();
            }
    }
}