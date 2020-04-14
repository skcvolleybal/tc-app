(function (app) {
  app.service('utilityService', function (dataLists) {
    _playerTypeClasses = {};

    function FirstOrDefault(list, checkFunction) {
      for (var i = 0; i < list.length; i++) {
        if (checkFunction(list[i])) {
          return list[i];
        }
      }

      return null;
    }

    function GetClassForPosition(typeId) {
      var playerTypeClass = _playerTypeClasses[typeId];
      if (!playerTypeClass) {
        var playerType = FirstOrDefault(dataLists.GetPlayerTypes(), function (
          playerType
        ) {
          return playerType.id == typeId;
        });
        switch (playerType.name) {
          case 'Spelverdeler':
            className = 'label-danger';
            break;
          case 'Midden':
            className = 'label-success';
            break;
          case 'Passer-loper':
            className = 'label-warning';
            break;
          case 'Diagonaal':
            className = 'label-info';
            break;
          case 'Libero':
            className = 'label-primary';
            break;
          case 'Trainingslid':
            className = 'label-costum';
            break;
          case 'Interesse':
            className = 'label-interesse';
            break;
          case 'Uitgeschreven':
            className = 'label-uitgeschreven';
            break;
          default:
            className = 'label-default';
            break;
        }
        _playerTypeClasses[typeId] = className;
        playerTypeClass = className;
      }
      return playerTypeClass;
    }

    return {
      FirstOrDefault: FirstOrDefault,
      GetClassForPosition: GetClassForPosition,
    };
  });
})(angular.module('tcApp'));
