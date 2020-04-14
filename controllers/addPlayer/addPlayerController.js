(function (app) {
   app.controller('addPlayerController', function ($scope, dataLists, Notification, utilityService, playerService, changeService, dragDropService) {
      $scope.lists = dataLists.GetLists();
      console.log($scope);
      $scope.player = {};
      $scope.player.teamId = $scope.lists.teams[0].id;
      $scope.player.trainingId = $scope.lists.trainingGroups[0].id;
      $scope.player.typeId = utilityService.FirstOrDefault($scope.lists.playerTypes, function (playerType) { return playerType.name === "Nog Niets" }).id;

      $scope.AddPlayer = function (player) {
         if (!$scope.player.name) {
            Notification.warning('Vul een naam in');
            return;
         }

         var playerId = playerService.AddPlayer(player);
         var change = {
            id: playerId,
            changeType: changeService.changeTypes.addPlayer,
            name: player.name,
            teamId: player.teamId,
            trainingId: player.trainingId,
            typeId: player.typeId
         }
         changeService.SaveChange(change);

         dragDropService.SetDraggablesAndDroppables();

         $scope.player.name = null;
      }
   })
})(angular.module('tcApp'));