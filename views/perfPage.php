<fieldset>
    <legend>Страница...</legend>
    <table class="pro-table">
		<button class="button_margin" ng-click="toggleWindow('main')">Назад</button>
        <tbody>
            <tr>
                <td class="label">
                    <label for="title">Название</label>
                </td>
                <td class="field">
					<textarea type="text" id="title" ng-model="perf_page.title"></textarea>
                </td>
                <td class="help">
                    Title - название в карточке спектакля
                </td>
            </tr>
			<tr>
                <td class="label">
                    <label for="slug">Slug</label>
                </td>
                <td class="field">
                    <input type="text" id="slug" ng-model="perf_page.slug">
                </td>
                <td class="help">
                    <button class="button_margin" ng-click="changePerfPageTitle()">Сгенерировать</button> Slug - url страницы
                </td>
            </tr>
            <tr>
                <td class="label">
                    <label for="type">Тип спектакля</label>
                </td>
                <td class="field">
                    <input type="text" id="type" ng-model="perf_page.type">
                </td>
                <td class="help">
                    Тип спектакля для страницы
                </td>
            </tr>
			<tr>
				<td class="label">
					<label for="video">Ссылка на youtube</label>
				</td>
				<td class="field">
					<input type="text" id="video" ng-model="perf_page.video">
				</td>
				<td class="help">
					https://www.youtube.com/watch?v=U3jUeBIwuMw<br /> из полной ссылки, указать последнюю часть - U3jUeBIwuMw
				</td>
			</tr>
            <tr>
                <td class="label">
                    <label>Фон полная версия (1862x1061)</label>
                </td>
                <td class="field">
                    <img ng-if="perf_page.files.page_bg.length" src="{{ perf_page.files.page_bg[0] }}" width="100px">
                    <div class="close_btn" ng-if="perf_page.files.page_bg.length" ng-click="delFile(perf_page.id, perf_page.files.page_bg[0])"></div>
                    <button class="button_margin" ng-if="perf_page.id && !perf_page.files.page_bg.length" ng-click="loadFile()">Загрузить</button>
                    <span type="text" ng-if="!perf_page.id">Загрузка доступна после создания страницы</span>
                </td>
                <td class="help">
                    Загрузка фона на страницу (полная версия)
                </td>
            </tr>
			<tr>
				<td class="label">
					<label>Фон мобильная версия (400x454)</label>
				</td>
				<td class="field">
					<img ng-if="perf_page.files.page_bg_mob.length" src="{{ perf_page.files.page_bg_mob[0] }}" width="100px">
					<div class="close_btn" ng-if="perf_page.files.page_bg_mob.length" ng-click="delFile(perf_page.id, perf_page.files.page_bg_mob[0])"></div>
					<button class="button_margin" ng-if="perf_page.id && !perf_page.files.page_bg_mob.length" ng-click="loadMobileFile()">Загрузить</button>
					<span type="text" ng-if="!perf_page.id">Загрузка доступна после создания страницы</span>
				</td>
				<td class="help">
					Загрузка фона на страницу (мобильная версия)
				</td>
			</tr>
			<tr>
				<td class="label">
					<label>Изображение для списка спектаклей (570х321)</label>
				</td>
				<td class="field">
					<img ng-if="perf_page.files.page_cover.length" src="{{ perf_page.files.page_cover[0] }}" width="100px">
					<div class="close_btn" ng-if="perf_page.files.page_cover.length" ng-click="delFile(perf_page.id, perf_page.files.page_cover[0])"></div>
					<button class="button_margin" ng-if="perf_page.id && !perf_page.files.page_cover.length" ng-click="loadCoverFile()">Загрузить</button>
					<span type="text" ng-if="!perf_page.id">Загрузка доступна после создания страницы</span>
				</td>
				<td class="help">
					Изображение для списка спектаклей
				</td>
			</tr>
			<tr>
				<td class="label">
					<label>Галлерея на странице (1920x1280)</label>
				</td>
				<td class="field">
					<div ng-if="perf_page.files.page_gellary.length" ng-repeat="v in perf_page.files.page_gellary">
						<img src="{{ v }}" width="100px">
						<div class="close_btn" ng-click="delFile(perf_page.id, v)"></div>
					</div>
					<button class="button_margin" ng-if="perf_page.id" ng-click="loadGallaryFile()">Загрузить</button>
					<span type="text" ng-if="!perf_page.id">Загрузка доступна после создания спектакля</span>
				</td>
				<td class="help">
					Изображения которые показываются в афише спектакля.
				</td>
			</tr>
            <tr>
                <td class="label">
                    <label>Описание в списке спектаклей</label>
                </td>
                <td class="field">
                    <textarea ng-model="perf_page.description"></textarea>
                </td>
                <td class="help">
                    Описание в списке спектаклей
                </td>
            </tr>
            <tr>
                <td class="label">
                    <label>Описание на странице - левое</label>
                </td>
                <td class="field">
                    <textarea ng-model="perf_page.left"></textarea>
                </td>
                <td class="help">
                    Описание на левой стороне страницы
                </td>
            </tr>
            <tr>
                <td class="label">
                    <label>Описание на странице - правое</label>
                </td>
                <td class="field">
                    <textarea ng-model="perf_page.right"></textarea>
                </td>
                <td class="help">
                    Описание на правой стороне страницы
                </td>
            </tr>
        </tbody>
    </table>
    <button class="button_margin" ng-click="savePerfPage()">Сохранить</button>
    <fieldset ng-if="perf_page.id">
        <button class="button_margin" ng-click="addPerfMain()">Добавить спектакль</button>
        <legend>Спектакли</legend>
        <table class="pro-table">
            <thead>
                <tr>
                    <th>Название</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <tr ng-repeat="v in perf_main">
                    <td class="headTd pointer" ng-click="openPerfMain(v.id)">
                        {{ v.title }}
                    </td>
                    <td class="headTd">
                        <div class="close_btn" ng-click="delPerfMain(v.id)"></div>
                    </td>
                </tr>
            </tbody>
        </table>
    </fieldset>
</fieldset>