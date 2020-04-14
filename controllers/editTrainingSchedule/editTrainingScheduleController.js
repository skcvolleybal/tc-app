(function (app) {
   app.controller('editTrainingScheduleController', function ($scope, dataLists, IOService) {
      console.log($scope);
      
      $scope.SaveTrainingInfo = function () {
         IOService.executeAction('SaveTrainingInfo', { teams: $scope.ngDialogData.teams})
         .then(function(){
            $scope.closeThisDialog();
         });
      }
   })
})(angular.module('tcApp'));