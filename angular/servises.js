var performancesServices = angular.module('performancesServices', ['ngResource']);
performancesServices.factory('performancesSrv', ['$resource', 'StateManager',
    function ($resource, StateManager) {
        var module = 'performancesManager'; //private variables
        var result = $resource('/performances/ajax', {}, {
            getPOST: {
                method: 'POST',
                url: '/performances/ajax',
                isArray: false,
                responseType: 'json',
            }
        });
        result.getData = function (params, action, callback, error) {
            StateManager.add(action);
            var primeParams = {module: module, action: action, dataKey: 'tags', params: params};
            if (typeof (callback) === 'function') {
                var t = this.getPOST(primeParams, function (data, headers) {
                    StateManager.remove(action);
                    return callback(data, headers);
                }, function (err) {
                    StateManager.remove(action);
                    if (err.data) {
                        cReport(err.data.error[0].msg);
                    } else {
                        throw new Error("Не верный формат ответа сервера");
                    }
                });
                return t;
            }

        };
        /**
         * Доп. метод для отправки данных
         * @param {type} params
         * @param {type} action
         * @param {type} callback
         * @returns {servises_L4.result.getData@call;getPOST}
         */
        result.setData = function (params, action, callback, error) {
            return result.getData(params, action, callback, error);
        };

        result.downloadFile = function (params, action) {
            return result.getData(params, action, function (response, getHeader) {
                cReport('RESPONSE:');
                cReport(response.fileBlob);
                var filename = "";
                var disposition = getHeader('Content-Disposition');
                if (disposition && disposition.indexOf('attachment') !== -1) {
                    var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                    var matches = filenameRegex.exec(disposition);
                    if (matches != null && matches[1])
                        filename = matches[1].replace(/['"]/g, '');
                }

                var type = getHeader('Content-Type');
                var blob = new Blob(response.fileBlob, {type: type});

                if (typeof window.navigator.msSaveBlob !== 'undefined') {
                    // IE workaround for "HTML7007: One or more blob URLs were revoked by closing the blob for which they were created. These URLs will no longer resolve as the data backing the URL has been freed."
                    window.navigator.msSaveBlob(blob, filename);
                } else {
                    var URL = window.URL || window.webkitURL;
                    var downloadUrl = URL.createObjectURL(blob);

                    if (filename) {
                        // use HTML5 a[download] attribute to specify filename
                        var a = document.createElement("a");
                        // safari doesn't support this yet
                        if (typeof a.download === 'undefined') {
                            window.location = downloadUrl;
                        } else {
                            a.href = downloadUrl;
                            a.download = filename;
                            document.body.appendChild(a);
                            a.click();
                        }
                    } else {
                        window.location = downloadUrl;
                    }

                    setTimeout(function () {
                        URL.revokeObjectURL(downloadUrl);
                    }, 100); // cleanup
                }
            }, function () {
                //error
            });
        }
        return result;
    }]);

performancesServices.factory('StateManager', ['$rootScope', '$log', function StatemManager($rootScope, $log) {
    var stateContainer = {};
    $rootScope.globalLoader = {};
    $rootScope.preloader = false;
    return {
        add: function (service) {
            stateContainer[service] = stateContainer[service] || 0;
            stateContainer[service]++;
            $rootScope.globalLoader[service] = true;
            $rootScope.preloader = true;
        },
        remove: function (service) {
            stateContainer[service] = stateContainer[service] || 0;
            if (stateContainer[service] > 0) {
                stateContainer[service]--;
            }

            if (stateContainer[service] < 1) {
                $rootScope.globalLoader[service] = false;
                $rootScope.preloader = false;
            }

        },
        getByName: function (service) {
            stateContainer[service] = stateContainer[service] || 0;
            return stateContainer[service];
        },
        clear: function () {
            stateContainer = {};
            $rootScope.globalLoader = {};
            $rootScope.preloader = false;
            return true;
        }
    };

}]);

performancesServices.factory('custom', [function() {
    return function(input, search) {
        if (!input) return input;
        if (!search) return input;
        var expected = ('' + search).toLowerCase();
        var result = {};
        angular.forEach(input, function(value, key) {
            var actual = ('' + value).toLowerCase();
            angular.forEach(value, function (val, k) {
                var actual = ('' + val).toLowerCase();
                if (actual.indexOf(expected) !== -1) {
                    result[key] = value;
                }
            });
        });
        return result;
    }
}]);

performancesServices.factory('privateFilter', [function() {
    return function(input, search, key_search) {
        if (!input) return input;
        if (!search) return input;
        var result = {};
        var keys = Object.keys(search);
        angular.forEach(input, function(value, key) {
            if(key_search !== undefined && key.toString().toLocaleLowerCase().indexOf(key_search.toString().toLocaleLowerCase()) === -1 && key_search.length > 0) return;
            var actual = ('' + value).toLowerCase();
            tmp = 0;
            angular.forEach(value, function (val, k) {
                var actual = ('' + val).toLowerCase();
                if (keys.includes(k) && actual.indexOf(search[k]) !== -1) {
                    tmp++;
                }
            });
            if(tmp == keys.length) result[key] = value;
        });
        return result;
    }
}]);