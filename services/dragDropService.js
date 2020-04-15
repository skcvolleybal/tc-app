(function (app) {
  app.service('dragDropService', function (
    utilityService,
    playerService,
    changeService,
    dataLists,
    Notification
  ) {
    function onDrop(event, ui) {
      var droppable = $(event.target);
      var draggable = $(ui.draggable);

      var newTeamId = droppable.attr('team-id');
      var playerId = draggable.attr('player-id');

      if (!playerId) return;

      var isTeam =
        utilityService.FirstOrDefault(dataLists.GetTeams(), function (team) {
          return team.id == newTeamId;
        }) != null;
      var isTrainingGroups =
        utilityService.FirstOrDefault(dataLists.GetTrainingGroups(), function (
          team
        ) {
          return team.id == newTeamId;
        }) != null;

      if (!isTeam && !isTrainingGroups) {
        throw 'geen team';
      }

      var name = draggable.attr('name');
      var typeId = draggable.attr('type-id');
      var type = draggable.attr('type');
      var oldTeamId = draggable.attr('team-id');
      var oldTrainingId = draggable.attr('training-id');
      var information = draggable.attr('information');

      if (oldTeamId == newTeamId) return;

      if (name === 'Jonathan Neuteboom') {
        if (isTeam && oldTeamId == 1001) {
          Notification.error('Oef, zou je dit nou wel doen...');
        }
        if (isTeam && newTeamId == 1001) {
          Notification.success('Goedzo!');
        }
      }

      var newPlayer = {
        changeType: changeService.changeTypes.updatePlayer,
        id: playerId,
        name: name,
        type: type,
        typeId: typeId,
        information: information,
        teamId: isTeam ? newTeamId : oldTeamId,
        trainingId: isTrainingGroups ? newTeamId : oldTrainingId,
      };

      playerService.ChangePlayer(newPlayer);
      changeService.SaveChange(newPlayer);

      SetDraggablesAndDroppables();
      SetTooltips();
    }

    function SetDraggablesAndDroppables() {
      $(function () {
        $('.draggable').draggable({ revert: true });
        $('.droppable').droppable({
          drop: function (event, ui) {
            onDrop(event, ui);
          },
        });
      });
    }

    function SetTooltips() {
      $(function () {
        $('[data-toggle="tooltip"]').tooltip({
          trigger: 'hover',
        });
      });
    }

    return {
      SetDraggablesAndDroppables: SetDraggablesAndDroppables,
      SetTooltips: SetTooltips,
    };
  });
})(angular.module('tcApp'));
