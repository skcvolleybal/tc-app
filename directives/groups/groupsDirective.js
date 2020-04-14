(function (app) {
  app.directive('groups', function (playerService) {
    return {
      templateUrl: 'directives/groups/groups.html',
      controller: function (
        $scope,
        playerService,
        changeService,
        dataLists,
        utilityService,
        dragDropService
      ) {
        $scope.playerService = playerService;
        $scope.dataLists = dataLists;
        $scope.GetClassForPosition = utilityService.GetClassForPosition;

        playerService.LoadAllPlayers().then(function () {
          dragDropService.SetDraggablesAndDroppables();
          dragDropService.SetTooltips();
        });

        $scope.ChangePosition = function (player, newType) {
          player.typeId = newType.id;
          player.changeType = changeService.changeTypes.updatePlayer;
          playerService.ChangePlayer(player);
          changeService.SaveChange(player);
        };

        $scope.DeleteTeam = function (teamId) {
          playerService.DeleteTeam(teamId);
          changeService.SaveChange({
            changeType: changeService.changeTypes.deleteTeam,
            id: teamId,
          });
          dataLists.DeleteTeam(teamId);
        };

        $scope.DeletePlayer = function (player) {
          playerService.DeletePlayer(player.id);
          changeService.SaveChange({
            changeType: changeService.changeTypes.deletePlayer,
            id: player.id,
          });
        };
      },
    };
  });
})(angular.module('tcApp'));
