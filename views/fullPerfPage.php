<fieldset>
    <legend>Спектакли</legend>
	<table class="pro-table">
		<thead>
			<tr>
				<th>Название</th>
				<th>Действия</th>
			</tr>
		</thead>
		<tbody ui-sortable="{'ui-floating': true}" ng-model="performances" ui-sortable-update="changePosition()">
			<tr ng-repeat="v in performances" ui-val>
				<td class="headTd pointer" ng-click="openPerfPage(v.id)">
					{{ v.title }}
				</td>
				<td class="headTd">
					<div class="close_btn" ng-click="delPerfPage(v.id)"></div>
				</td>
			</tr>
		</tbody>
	</table>
    <button class="button_margin" ng-click="addPerfPage()">Добавить страницу спектакля</button>
</fieldset>