(function (app) {
  app.directive('legenda', function () {
    return {
      scope: {},
      templateUrl: 'directives/legenda/legenda.html',
      controller: function ($scope, utilityService, dataLists) {
        $scope.playerTypes = dataLists.GetPlayerTypes();
        $scope.GetClassForPosition = utilityService.GetClassForPosition;

      },
    };
  });
})(angular.module('tcApp'));
