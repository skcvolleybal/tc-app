(function (app) {
  app.directive('searchBar', function () {
    return {
      templateUrl: 'directives/searchBar/searchBar.html',
      controller: function ($scope, IOService) {
        $scope.Search = function () {
          IOService.executeAction('FindPlayers', {
            searchString: $scope.searchString,
          }).then(function (result) {
            console.log(result);
            $scope.searchList = result.playerList;
          });
        };
      },
    };
  });
})(angular.module('tcApp'));
