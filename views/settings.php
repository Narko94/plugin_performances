<?php
if (!defined('IN_CMS')) exit();
require_once 'header.php';
?>
<div ng-app="performancesApp" ng-controller="mainController">
    <div>
		<!-- ФОРМЫ ДЛЯ РАБОТЫ СО СПЕКТАКЛЯМИ -->
		<div ng-if="!toggle.perf_page && !toggle.perf_main" ng-include src="'/admin/plugin/performances/fullPerfPage'"></div>
		<div ng-if="toggle.perf_page && !toggle.perf_main" ng-include src="'/admin/plugin/performances/perfPage'"></div>
		<div ng-if="toggle.perf_main" ng-include src="'/admin/plugin/performances/perfMain'"></div>
		<!-- ФОРМЫ ДЛЯ РАБОТЫ СО СПЕКТАКЛЯМИ -->
        <!-- МОДАЛЬНОЕ ОКНО -->
		<div ng-if="toggle.loadFile" ng-include src="'/admin/plugin/performances/windowLoadFile'"></div>
		<div ng-if="toggle.perf_playbill" ng-include src="'/admin/plugin/performances/windowPerfPlaybill'"></div>
        <!-- МОДАЛЬНОЕ ОКНО -->
        <div class="preloader" ng-show="preloader">
            <img src='/wolf/icons/preloader.gif' title='Идет загрузка' />
        </div>
    </div>
</div>