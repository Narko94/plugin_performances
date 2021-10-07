'use strict';

var performancesControllers = angular.module('performancesControllers', ['ngAnimate', 'ngSanitize']);
performancesControllers.directive('projectId', function() {
    return function(scope, element, attrs) {
        scope.projectID = attrs.projectId;
    };
});