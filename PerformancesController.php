<?php
if(!defined('IN_CMS')) exit();

class PerformancesController extends PluginController {
	private $post;
	private $files;
    private $page_id = 144;

	/**
	 * Обработка ajax запросов
	 */
	public function ajax() {
		$this->_checkPermission();
		$data['success'] = false;
		if(!(ob_get_level() > 0)) ob_start();
		if(isset($_REQUEST['action']) && method_exists($this, $_REQUEST['action'])) {
			try {
				$method          = $_REQUEST['action'];
				$data['data']    = $this->$method();
				$data['success'] = true;
				$data['content'] = ob_get_clean();
				print json_encode($data, JSON_NUMERIC_CHECK);
				exit();
			} catch(Exception $e) {
				$data['success'] = false;
				$data['trace']   = $e->getTrace();
				$data['msg']     = $e->getMessage();
				$data['data']    = [];
				print json_encode($data, JSON_NUMERIC_CHECK);
				exit();
			}
		}
		exit();
	}

	/**
	 * Проверка на доступ к плагину (из админки)
	 */
	private function _checkPermission() {
		AuthUser::load();
		if(!AuthUser::isLoggedIn()) {
			redirect(get_url('login'));
		} else if(!AuthUser::hasPermission('performances')) {
			Flash::set('error', 'Вы не имеете прав доступа к запрашиваемой странице!');
			redirect(get_url());
		}
	}

	public function __construct() {
		$this->setLayout('backend');
		$this->post     = new Post();
		$this->files    = new Files();
	}

	/**
	 * Страница настроек
	 */
	public function settings() {
		$this->_checkPermission();
		$this->display('performances/views/settings', []);
	}

	public function index() {
		$this->_checkPermission();
		$this->display('performances/views/settings', []);
	}

    public function windowLoadFile() {
        $this->includeWindow(__FUNCTION__);
    }

    public function fullPerfPage() {
        $this->includeWindow(__FUNCTION__);
    }

    public function perfMain() {
        $this->includeWindow(__FUNCTION__);
    }

    public function perfPage() {
        $this->includeWindow(__FUNCTION__);
    }

    public function windowPerfPlaybill() {
        $this->includeWindow(__FUNCTION__);
    }

    private function includeWindow($file) {
        $this->_checkPermission();
        require_once PERFORMANCES_ROOT . DS . 'views' . DS . $file . '.php';
    }

    private function getPerformances() {
		$data = (new PerfPage())->search([], ['id' => 0], ['>'], true, 'position');
	    ksort($data, SORT_NUMERIC);
        return ['performances' => $data];
    }

    private function openPerfPage() {
        return [
            'perf_page' => (new PerfPage())->initFromDB(['id' => $this->post->id])->getFiles(),
            'perf_main' => (new PerfMain())->search(['perf_page_id' => $this->post->id])
        ];
    }

    private function openPerfMain() {
        return [
            'perf_main'     => (new PerfMain())->initFromDB(['id' => $this->post->id])->getFiles(),
            'perf_playbill' => (new PerfPlaybill())->search(['perf_main_id' => $this->post->id], [], [], true, false, "*", false, 'updateFormatDate')
        ];
    }

    private function openPerfPlaybill() {
		return [
			'data_perf_playbill' => (new PerfPlaybill())->initFromDB(['id' => $this->post->id])->updateFormatDate()
		];
    }

    public function uploading($class = false, $type = false, $id = false, $perf_page_id = false) {
        $this->_checkPermission();
        if(!$class || !class_exists($class) || !$type) return print "Класс: {$class} || тип: {$type} - не найден";
        if(!$this->files->getToArray() && count($this->files->getToArray()) === 0) return false;

        $data = [];
        foreach ($class::TABLE_STRUCT as $val) {
            if(isset($$val))
                $data["{{$val}}"] = $$val;
        }

        foreach ($this->files->getToArray() as $key) {
            $dir = rtrim(str_replace(array_keys($data), array_values($data), $class::FILEUPLOAD[$type]), "/");
            $pFiles = (new PerformancesFiles($dir))->getDirFile();
            move_uploaded_file($key->tmp_name, $dir . DS . $key->name);
        }
    }

    private function delFiles() {
        if(!class_exists($this->post->cl)) return false;
        $class = $this->post->cl;
        $data = [];

        foreach ($class::TABLE_STRUCT as $struct) {
            if(property_exists($this->post, $struct))
                $data[$struct] = $this->post->{$struct};
        }

        $class = new $class($data);
        $files = [];
        $_files = $class->getFiles();

        if(property_exists($_files, 'files')) {
            foreach ($_files->files as $_file) {
                $files = array_merge($files, $_file);
            }
        }

        if(in_array($this->post->file, $files))
            unlink(CMS_ROOT . $this->post->file);
    }

    private function savePerfPage() {
        $perfPage = new PerfPage();
        if($perfPage->setData($this->post->getToArray())->save())
            $this->post->id = (property_exists($this->post, 'id') ? $this->post->id : $perfPage->lastInsertId());
        return $this->openPerfPage();
    }

    private function savePerfMain() {
        $perfMain = new PerfMain();
        if($perfMain->setData($this->post->getToArray())->save())
            $this->post->id = property_exists($this->post, 'id') ? $this->post->id : $perfMain->lastInsertId();
        return $this->openPerfMain();
    }

    private function savePerfPlaybill() {
        $perfPlaybill = new PerfPlaybill();
        $this->post->date = $perfPlaybill->iTime(2, 1, "", ['day' => $this->post->date]);
        if($perfPlaybill->setData($this->post->getToArray())->save())
            $this->post->id = $this->post->perf_main_id;
        return $this->openPerfMain();
    }

    private function delPerfPlaybill() {
        (new PerfPlaybill($this->post->getToArray()))->del();
        $this->post->id = $this->post->perf_main_id;
        return $this->openPerfMain();
    }

    private function delPerfPage() {
        (new PerfPage($this->post->getToArray()))->del();
        return $this->getPerformances();
    }

    private function delPerfMain() {
        (new PerfMain($this->post->getToArray()))->del();
        $this->post->id = $this->post->perf_page_id;
        return $this->openPerfPage();
    }

    private function updatePostion() {
		$perfPage = new PerfPage();
		$update = $update_id = [];
		foreach($this->post->data as $position => $val) {
			$update['position'][] = $position;
			$update_id[] = $val['id'];
		}
		$perfPage->updateArr($update, ["id" => $update_id], $perfPage::TABLE);

		return $this->getPerformances();
    }

    /* ДЛЯ FRONTEND РАЗРАБОТЧИКОВ */
    public function getPage($slug = false) {
        $perfPage = (new PerfPage())->initFromDB(['slug' => $slug]);
        if($perfPage->id === NULL)
            pageNotFound();
        $page = Page::findById($this->page_id);
        $page->perfPage = $perfPage;
        $page->_executeLayout();
    }
	/* ДЛЯ FRONTEND РАЗРАБОТЧИКОВ */
}