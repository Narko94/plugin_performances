<fieldset>
	<button class="button_margin" ng-click="openPerfPage(perf_main.perf_page_id)">Назад</button>
    <legend>Добавление/редактирование спектакля</legend>
    <table class="pro-table">
        <tbody>
            <tr>
                <td class="label">
                    <label for="title">Название спектакля</label>
                </td>
                <td class="field">
					<textarea type="text" id="title" ng-model="perf_main.title"></textarea>
                </td>
                <td class="help">
                    Title - название спектакля
                </td>
            </tr>
            <tr>
                <td class="label">
                    <label>Описание в афише</label>
                </td>
                <td class="field">
                    <textarea ng-model="perf_main.description"></textarea>
                </td>
                <td class="help">
                    Описание в афише
                </td>
            </tr>
            <tr>
                <td class="label">
                    <label>Тип спектакля</label>
                </td>
                <td class="field">
                    <input type="text" ng-model="perf_main.type">
                </td>
                <td class="help">
                    Например: мелодрама
                </td>
            </tr>
            <tr>
                <td class="label">
                    <label>Премьера</label>
                </td>
                <td class="field">
                    <input type="checkbox" ng-model="perf_main.premiere" ng-true-value="1" ng-false-value="0" id="premiere">
                    <label for="premiere"></label>
                </td>
                <td class="help"></td>
            </tr>
            <tr>
                <td class="label">
                    <label>Рейтинг</label>
                </td>
                <td class="field">
                    <input type="text" ng-model="perf_main.rating">
                </td>
                <td class="help"></td>
            </tr>
            <tr>
                <td class="label">
                    <label>Видео/изображение для главной (1920x700)</label>
                </td>
                <td class="field">
                    <img ng-if="perf_main.files.perf_main.length && !is_video(perf_main.files.perf_main)" src="{{ perf_main.files.perf_main[0] }}" width="100px">
                    <video ng-if="perf_main.files.perf_main.length && is_video(perf_main.files.perf_main)" src="{{ perf_main.files.perf_main[0] }}" width="100px" autoplay muted loop playsinline></video>
                    <div class="close_btn" ng-if="perf_main.files.perf_main.length" ng-click="delPerfMainFile(perf_main.id, perf_main.files.perf_main[0])"></div>
                    <button class="button_margin" ng-if="perf_main.id && !perf_main.files.perf_main.length" ng-click="loadPerfMainFile()">Загрузить</button>
                    <span type="text" ng-if="!perf_main.id">Загрузка доступна после создания спектакля</span>
                </td>
                <td class="help">
                    Видео/Изображение на главной странице (полная версия)
                </td>
            </tr>
            <tr>
                <td class="label">
                    <label>Изображение для главной (моб.) (400x454)</label>
                </td>
                <td class="field">
                    <img ng-if="perf_main.files.perf_main_mob.length" src="{{ perf_main.files.perf_main_mob[0] }}" width="100px">
                    <div class="close_btn" ng-if="perf_main.files.perf_main_mob.length" ng-click="delPerfMainFile(perf_main.id, perf_main.files.perf_main_mob[0])"></div>
                    <button class="button_margin" ng-if="perf_main.id && !perf_main.files.perf_main_mob.length" ng-click="loadPerfMainFileMob()">Загрузить</button>
                    <span type="text" ng-if="!perf_main.id">Загрузка доступна после создания спектакля</span>
                </td>
                <td class="help">
                    Изображение на главную страницу, для мобильной версии
                </td>
            </tr>
            <tr>
                <td class="label">
                    <label>Изображения для афиши (236x135)</label>
                </td>
                <td class="field">
                    <div ng-if="perf_main.files.perf_poster.length" ng-repeat="v in perf_main.files.perf_poster">
                        <img src="{{ v }}" width="100px">
                        <div class="close_btn" ng-click="delPerfMainFile(perf_main.id, v)"></div>
                    </div>
                    <button class="button_margin" ng-if="perf_main.id" ng-click="loadPerfMainFilePoster()">Загрузить</button>
                    <span type="text" ng-if="!perf_main.id">Загрузка доступна после создания спектакля</span>
                </td>
                <td class="help">
                    Изображения которые показываются в афише спектакля.
                </td>
            </tr>
        </tbody>
    </table>
    <button class="button_margin" ng-click="savePerfMain()">Сохранить</button>
    <button class="button_margin" ng-if="perf_main.id" ng-click="addPerfPlaybill()">Добавить расписание для спектакля</button>
    <table class="pro-table">
        <thead>
            <tr>
                <th>Ссылка</th>
                <th>Дата</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <tr ng-repeat="v in perf_playbill">
                <td class="headTd pointer" ng-click="openPerfPlaybill(v.id)">
                    {{ v.url }}
                </td>
                <td class="headTd pointer" ng-click="openPerfPlaybill(v.id)">
                    {{ v.date }}
                </td>
                <td class="headTd">
                    <div class="close_btn" ng-click="delPerfPlaybill(v.id)"></div>
                </td>
            </tr>
        </tbody>
    </table>
</fieldset>