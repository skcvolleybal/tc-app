(function (app) {
   app.directive('player', function (IOService, dataLists, playerService, dragDropService) {
      return {
         templateUrl: '/tc-app/directives/player/player.html',
         controller: function ($scope, dataLists) {
            $scope.EditPlayer = function (playerId) {
               playerService.GetPlayer(playerId)
                  .then(function (player) {
                     $scope.selectedPlayer = player;
                  });
            }

            $scope.CloseEditPlayerDialog = function () {
               $scope.selectedPlayer = null;
            }

            $scope.SavePlayer = function () {
               IOService.executeAction('UpdatePlayerInformation', {
                  id: $scope.selectedPlayer.id,
                  information: $scope.selectedPlayer.information
               }).then(function () {
                  playerService.SetInformation($scope.selectedPlayer.id, $scope.selectedPlayer.information);
                  dragDropService.SetTooltips();
               });
            }
         }
      }
   })
})(angular.module('tcApp'));