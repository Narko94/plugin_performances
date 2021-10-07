<div class="addEditComp">
    <div class="content">
        <div class="del_btn companyClose" ng-click="togglePerfPlaybill()"></div>
        <div class="back_content">
            <div class="companyList">
                <table class="pro-table">
                    <tbody>
                        <tr>
                            <td class="label">
                                <label for="title">Дата спектакля (01.01.2021 10:00)</label>
                            </td>
                            <td class="field">
                                <input type="text" id="inset" ng-model="data_perf_playbill.date">
                            </td>
                        </tr>
                        <tr>
                            <td class="label">
                                <label for="title">Ссылка</label>
                            </td>
                            <td class="field">
                                <input type="text" id="inset" ng-model="data_perf_playbill.url">
                            </td>
                        </tr>
                    </tbody>
                </table>
                <button ng-click="savePerfPlaybill()" class="button_margin">Сохранить</button>
            </div>
        </div>
    </div>
</div>