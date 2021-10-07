'use strict';

performancesControllers.controller('mainController', ['$scope', '$routeParams', 'performancesSrv', 'StateManager', '$animate', '$http',
	function ($scope, $routeParams, performancesSrv, StateManager, $animate, $http) {
		/* СПЕКТАКЛИ */
		$scope.performances = {};

		$scope.perf_page = {};
		$scope.perf_main = {};
		$scope.perf_playbill = {};
		$scope.data_perf_playbill = {};

		$scope.updateMethod = {
			method: null,
			id: 	null
		};

		$scope.toggle = {
			perf_page: 		false,
			perf_main: 		false,
			perf_playbill: 	false,
			loadFile: 		false
		};

		$scope.loadFileData = {
			class: 	null,
			type: 	null
		};

		$scope.data = {
			perf_page: 		false,
			perf_main: 		false,
			perf_playbill: 	false
		};

		//'id', 'slug', 'title', 'left', 'right', 'video', 'type', 'description', 'position'
		$scope.default = {
			perf_page: {
				title: 	"",
				left: 	"",
				right: 	"",
				description: "",
				slug: "",
				video: "",
				type: "",
				position: 0
			},
			perf_main: {
				title: 			"",
				description: 	"",
				type: 			"",
				premiere: 		0,
				rating: 		"",
				perf_page_id: 	-1
			},
			data_perf_playbill: {
				url: 			"",
				date: 			"",
				perf_main_id: 	-1
			}
		};

		/* ДЕФОЛТНЫЕ МЕТОДЫ ДЛЯ ВСЕХ ПЛАГИНОВ НА ANGULAR */
		/* МЕТОД ОБРАБОТКИ ОШИБОК */
		$scope.alertError = (data) => {
			cReport('alertError');
			cReport(data);
			if (data.data === undefined || data.data === null) return false;
			if (data.data.error !== undefined) {
				alert(data.data.error);
				return false;
			}
			return true;
		};
		/* МЕТОД ОБРАБОТКИ ОШИБОК */

		/* AJAX ЗАПРОС
		* call - передается название метода
		* params - передаваемые параметры
		* method - метод на стороне backend (Controller)
		*/
		$scope.srv = (call, params, method) => {
			cGroup('start', call);
			if (method === undefined) {
				cReport('method set -> ' + call);
				method = call;
			}
			performancesSrv.getData(params, method, (data) => {
				if ($scope.alertError(data) === false) return cGroup('end');
				angular.forEach(data.data, (val, key) => {
					$scope[key] = val;
				});
				cGroup('end');
			});
		};

		$scope.toggleWindow = (name) => {
			$scope.defaultData(name);
			Object.keys($scope.toggle).forEach(v => {
				if(v === name)
					$scope.toggle[name] = true;
				else $scope.toggle[v] = false;
			});
		};

		$scope.defaultData = (type) => {
			if($scope.default && $scope.default[type])
				$scope[type] = angular.copy($scope.default[type]);
			if(type === 'perf_main') {
				$scope.perf_main.perf_page_id = $scope.perf_page.id;
				$scope.perf_main.title = $scope.perf_page.title;
			}
		};
		/* ДЕФОЛТНЫЕ МЕТОДЫ ДЛЯ ВСЕХ ПЛАГИНОВ НА ANGULAR */

		$scope.getPerformances = () => {
			$scope.toggleWindow('main');
			$scope.srv('getPerformances', {}, 'getPerformances');
		};

		$scope.getPerformances();

		$scope.delPerfPage = (id) => {
			$scope.srv('delPerfPage', {id: id}, 'delPerfPage');
		};

		$scope.openPerfPage = (id) => {
			$scope.toggleWindow('perf_page');
			$scope.srv("openPerfPage", {id: id}, "openPerfPage");

			$scope.data.perf_main = $scope.data.perf_playbill = false;
			$scope.data.perf_page = id;
		};

		$scope.addPerfPage = () => {
			$scope.toggleWindow('perf_page');
			$scope.perf_main = $scope.perf_playbill = {};
		};

		$scope.addPerfMain = () => {
			$scope.toggleWindow('perf_main');
			$scope.perf_playbill = {};
		};

		$scope.savePerfPage = () => {
			$scope.srv("savePerfPage", $scope.perf_page, "savePerfPage");
		};

		$scope.savePerfMain = () => {
			$scope.srv("savePerfMain", $scope.perf_main, "savePerfMain");
		};

		$scope.savePerfPlaybill = () => {
			$scope.data_perf_playbill.perf_main_id = $scope.perf_main.id;
			$scope.srv("savePerfPlaybill", $scope.data_perf_playbill, "savePerfPlaybill");
		};

		$scope.openPerfMain = (id) => {
			$scope.toggleWindow('perf_main');
			$scope.srv("openPerfMain", {id: id}, "openPerfMain");

			$scope.data.perf_main = id;
		};

		$scope.delPerfMain = (id) => {
			$scope.srv('delPerfMain', {id: id, perf_page_id: $scope.perf_page.id}, 'delPerfMain');
		};

		$scope.addPerfPlaybill = () => {
			$scope.toggle.perf_playbill = true;
			$scope.defaultData('data_perf_playbill');
		};

		$scope.togglePerfPlaybill = () => {
			$scope.toggle.perf_playbill ^= true;
		};

		$scope.openPerfPlaybill = (id) => {
			$scope.toggle.perf_playbill = true;
			$scope.srv("openPerfPlaybill", {id: id}, "openPerfPlaybill");

			$scope.data.perf_playbill = id;
		};

		$scope.delPerfPlaybill = (id) => {
			$scope.srv('delPerfPlaybill', {id: id, perf_main_id: $scope.perf_main.id}, 'delPerfPlaybill');
		};

		$scope.changePerfPageTitle = () => {
			$scope.perf_page.slug = toSlug($scope.perf_page.title);
		};

		/* ЗАГРУЗКА ФАЙЛОВ */
		$scope.toggleLoadFile = () => {
			$scope.toggle.loadFile ^= true;
		};

		$scope.mainLoadFiles = () => {
			var $input = $("#files");
			var fd = new FormData;

			Object.values($input.prop("files")).forEach((value, key) => {
				fd.append("file_" + key, value);
			});

			$.ajax({
				url: "/performances/uploading/" + $scope.loadFileData.class + "/" + $scope.loadFileData.type + "/" + $scope.loadFileData.id + "/" + ($scope.loadFileData.perf_page_id || 0),
				data: fd,
				processData: false,
				contentType: false,
				type: "POST",
				success: function (data) {
					$scope[$scope.updateMethod.method]($scope.updateMethod.id);
				}
			});

		};

		$scope.loadFile = () => {
			// логика загрузка файлов...
			$scope.toggleLoadFile();
			$scope.loadFileData = {
				class: "PerfPage",
				type: "page_bg",
				id: $scope.perf_page.id
			};

			$scope.updateMethod = {
				method: "openPerfPage",
				id: $scope.perf_page.id
			};
		};

		$scope.loadGallaryFile = () => {
			$scope.toggleLoadFile();
			$scope.loadFileData = {
				class: "PerfPage",
				type: "page_gellary",
				id: $scope.perf_page.id
			};

			$scope.updateMethod = {
				method: "openPerfPage",
				id: $scope.perf_page.id
			};
		};

		$scope.loadMobileFile = () => {
			// логика загрузка файлов...
			$scope.toggleLoadFile();
			$scope.loadFileData = {
				class: "PerfPage",
				type: "page_bg_mob",
				id: $scope.perf_page.id
			};

			$scope.updateMethod = {
				method: "openPerfPage",
				id: $scope.perf_page.id
			};
		};

		$scope.loadCoverFile = () => {
			// логика загрузка файлов...
			$scope.toggleLoadFile();
			$scope.loadFileData = {
				class: "PerfPage",
				type: "page_cover",
				id: $scope.perf_page.id
			};

			$scope.updateMethod = {
				method: "openPerfPage",
				id: $scope.perf_page.id
			};
		};

		$scope.loadPerfMainFile = () => {
			// логика загрузка файлов...
			$scope.toggleLoadFile();
			$scope.loadFileData = {
				class: "PerfMain",
				type: "perf_main",
				id: $scope.perf_main.id,
				perf_page_id: $scope.perf_page.id
			};

			$scope.updateMethod = {
				method: "openPerfMain",
				id: $scope.perf_main.id
			};
		};

		$scope.loadPerfMainFileMob = () => {
			// логика загрузка файлов...
			$scope.toggleLoadFile();
			$scope.loadFileData = {
				class: "PerfMain",
				type: "perf_main_mob",
				id: $scope.perf_main.id,
				perf_page_id: $scope.perf_page.id
			};

			$scope.updateMethod = {
				method: "openPerfMain",
				id: $scope.perf_main.id
			};
		};

		$scope.loadPerfMainFilePoster = () => {
			// логика загрузка файлов...
			$scope.toggleLoadFile();
			$scope.loadFileData = {
				class: "PerfMain",
				type: "perf_poster",
				id: $scope.perf_main.id,
				perf_page_id: $scope.perf_page.id
			};

			$scope.updateMethod = {
				method: "openPerfMain",
				id: $scope.perf_main.id
			};
		};
		/* ЗАГРУЗКА ФАЙЛОВ */

		/* УДАЛЕНИЕ ФАЙОРВ */
		$scope.delFile = (id, file) => {
			// логика удаления файлов...
			$scope.srv("delFile", {
				cl: "PerfPage",
				id: id,
				file: file
			}, "delFiles");
			$scope.openPerfPage(id);
		};

		$scope.delPerfMainFile = (id, file) => {
			// логика удаления файлов...
			$scope.srv("delPerfMainFile", {
				cl: "PerfMain",
				id: id,
				file: file,
				perf_page_id: $scope.perf_page.id
			}, "delFiles");
			$scope.openPerfMain(id);
		};
		/* УДАЛЕНИЕ ФАЙОРВ */

		$scope.is_video = (file) => {
			return file.toString().match(/.mp4/) !== null;
		};

		$scope.changePosition = () => {
			$scope.srv("changePosition", {data: $scope.performances}, "updatePostion");
		};
	}
]);