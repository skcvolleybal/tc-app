(function (app) {
   app.controller('editTeamOrderController', function ($scope, dataLists, IOService, playerService, changeService) {
      $scope.orderList = {
         teams: null,
         type: null
      };

      if ($scope.ngDialogData.isTeamsActive) {
         $scope.orderList.teams = dataLists.GetTeams();
         $scope.orderList.type = "team";
      }
      else {
         $scope.orderList.teams = dataLists.GetTrainingGroups();
         $scope.orderList.type = "training";
      }

      $scope.SaveOrder = function () {
         var children = $("#sortableList").children();
         var orderCounter = 1;
         var teamOrder = [];
         angular.forEach(children, function (child) {
            var teamId = $(child).attr('team-id');
            var change = {
               changeType: changeService.changeTypes.updateTeam,
               id: teamId,
               sequence: orderCounter++
            }
            changeService.SaveChange(change);
            teamOrder.push(change);
         });
         
         playerService.OrderTeams(teamOrder, $scope.orderList.type);
         dataLists.OrderTeams(teamOrder, $scope.orderList.type);
         $scope.closeThisDialog();
      }
   });
})(angular.module('tcApp'))


