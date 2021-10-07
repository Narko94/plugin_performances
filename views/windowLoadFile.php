<div class="addEditComp">
	<div class="content">
		<div class="del_btn companyClose" ng-click="toggleLoadFile()"></div>
		<div class="back_content">
			<div class="companyList">
				<table class="pro-table">
					<tbody>
						<tr>
							<td><label>Загрузка файла</label></td>
							<td>
								<form name="tested" id="tested" method="post" enctype="multipart/form-data">
									<input type="file" id="files" name="files" multiple>
								</form>
							</td>
						</tr>
					</tbody>
				</table>
				<button ng-click="mainLoadFiles()" class="button_margin">Загрузить</button>
			</div>
		</div>
	</div>
</div>