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
        var playerType = FirstOrDefault(dataLists.GetPlayerTypes(), function (playerType) {
          return playerType.id == typeId;
        });
        switch (playerType.name) {
          case 'Spelverdeler':
            background = '#d9534f'
            break;
          case 'Midden':
            // styles = setMiddenStyle(typeId, playerId)
            background = '#5cb85c';
            break;
          case 'Passer-loper':
            // styles = setPasserStyle(typeId, playerId)
            background = '#f0ad4e';
            break;
          case 'Diagonaal':
            background = '#1bc5f8';
            break;
          case 'Libero':
            background ='#337ab7';
            break;
          case 'Trainingslid':
            styles.background ='#f8e71c';
            break;
          case 'Interesse':
            // styles.color = '#5e5e5e';
            // styles.border = '1px solid #808080';
            styles.background ='#ff6f61';
            // styles['font-style'] = 'italic';
            break;
          case 'Uitgeschreven':
            background ='#555';
            // styles['text-decoration'] = 'line-through';
            break;
          default:
            background = '#ff6f61';
            break;
        }
        // _playerTypeClasses[typeId] = styles;
        // playerTypeClass = styles;
      }
      return background;
    }

    function GetInteresseForPosition(interestId) {
      console.log("interestId" + interestId)
      switch (interestId) {
        case 1: //'Interesse-spelverdeler':
          border = '2px solid #d9534f'
          break;
        case 2: // 'Interesse-midden':
          // styles = setMiddenInteresse()
          border = '2px solid #5cb85c';
          break;
        case 3: //'Interesse-passer-loper':
          border = '2px solid #f0ad4e';
          break;
        case 4: //'Interesse-diagonaal':
          border = '2px solid #1bc5f8';
          break;
        case 5: //'Interesse-libero':
          border =  '2px solid #337ab7';
          break;
        case 6: //'Geen':
          border = '2px solid white';
          break;
        default:
          border = '2px solid white';
          break;
      }
      return border
    }

    function GetStyleForPosition(typeId, interesseId) {
      styles = {'background' : '#eee',
                'border': '2px solid blue'
      }
      styles.background = GetClassForPosition(typeId);
      styles.border = GetInteresseForPosition(interesseId);

      console.log(styles)
      return styles

    }

    return {
      FirstOrDefault: FirstOrDefault,
      GetClassForPosition: GetClassForPosition,
      GetStyleForPosition: GetStyleForPosition,
      GetInteresseForPosition: GetInteresseForPosition
    };
  });
})(angular.module('tcApp'));
