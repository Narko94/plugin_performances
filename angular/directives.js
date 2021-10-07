function convertToNumber() {
    return {
        require: 'ngModel',
        link: function(scope, element, attrs, ngModel) {
            ngModel.$parsers.push(function(val) {
                return val != null ? parseInt(val, 10) : null;
            });
            ngModel.$formatters.push(function(val) {
                return val != null ? val.toString() : null;
            });
        }
    };
};
angular
    .module('performancesApp')
    .directive('convertToNumber', convertToNumber)
    .filter('jsonLimitTo', function () {
        return function(obj, start, end){
            var keys = Object.keys(obj);
            if(keys.length < 1){
                return [];
            }

            var ret = new Object,
                count = leng = 0;
            angular.forEach(keys, function(key, arrayIndex){
                if((count >= start && end === undefined) || (end !== undefined && (leng < end || count >= start))){
                    leng++;
                } else {
                    leng++;
                    ret[key] = obj[key];
                    count++;
                }
            });
            return ret;
        };
    })
    .filter('custom', function() {
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
    })
    .directive('ngCopyValue', function() {
        return {
            restrict: 'A',
            link:link
        };
        function link($scope, element, attrs) {
            $scope.copyValue = 'NET';
            element.bind('click',function(){

                var range = document.createRange();
                range.selectNode(element[0]);
                window.getSelection().removeAllRanges();
                window.getSelection().addRange(range);
                var successful = document.execCommand('copy');

                var msg = successful ? 'successful' : 'unsuccessful';
                console.log('Copying text command was ' + msg);
                window.getSelection().removeAllRanges();
            });
        }

    })