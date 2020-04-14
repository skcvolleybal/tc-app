(function (app) {
   app.controller('loginController', function ($scope, IOService, Notification, loginService, dataLists) {
      $scope.login = function () {
         if (!$scope.username || !$scope.password) {
            Notification.error("Vul eerst alles in");
            return;
         }

         IOService.executeAction('Login', {
            username: $scope.username,
            password: $scope.password
         }).then(function (result) {
            dataLists.SetLists(result.dataLists);
            loginService.setLoggedIn(true);
            $scope.closeThisDialog();
         });
      }
   })
})(angular.module('tcApp'));