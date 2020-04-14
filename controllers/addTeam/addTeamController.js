(function (app) {
   app.controller('addTeamController', function ($scope, IOService, Notification, playerService, changeService, dataLists, dragDropService) {
      $scope.teamTypes = [
         {
            name: 'Team',
            type: 'team'
         }, {
            name: 'Trainingsgroep',
            type: 'training'
         }
      ];

      $scope.AddTeam = function () {
         if (!$scope.teamType || !$scope.name) {
            Notification.error("Vul eerst alles in");
            return;
         }

         var newTeam = {
            changeType: changeService.changeTypes.addTeam,
            type: $scope.teamType,
            name: $scope.name
         };

         newTeam = playerService.AddTeam(newTeam);
         changeService.SaveChange(newTeam);
         dataLists.AddTeam(newTeam);

         dragDropService.SetDraggablesAndDroppables();

         $scope.closeThisDialog();
      }
   })
})(angular.module('tcApp'));