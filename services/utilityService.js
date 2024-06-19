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
      const style = {}
      if (!playerTypeClass) {
        var playerType = FirstOrDefault(dataLists.GetPlayerTypes(), function (playerType) {
          return playerType.id == typeId;
        });
        switch (playerType.name) {
          case 'Spelverdeler':
            style.background = '#d9534f'
            style['font-style'] = 'normal';
            break;
          case 'Midden':
            // styles = setMiddenStyle(typeId, playerId)
            style.background = '#5cb85c';
            style['font-style'] = 'normal';
            break;
          case 'Passer-loper':
            // styles = setPasserStyle(typeId, playerId)
            style.background = '#f0ad4e';
            style['font-style'] = 'normal';
            break;
          case 'Diagonaal':
            style.background = '#1bc5f8';
            style['font-style'] = 'normal';
            break;
          case 'Libero':
            style.background ='#337ab7';
            style['font-style'] = 'normal';
            break;
          case 'Trainingslid':
            style.background = '#8a8888fc'
            style.border = 'border 1px solid #808080'
            style['font-style'] = 'normal';
            break;
          case 'Interesse':
            style.background = '#8a8888fc'
            style.border = 'border 1px solid #808080'
            style['font-style'] = 'italic';
            break;
          case 'Uitgeschreven':
            style.background ='#555';
            style.color = '#eee';
            style.border = 'border 1px solid #808080'
            style['text-decoration'] = 'line-through';
            style['font-style'] = 'normal';
            break;
          case 'Nog Niets':
            style.background = '#3b3333'
            style['font-style'] = 'normal';
            break;
          default:
            style.background = '#ff6f61';
            break;
        }
        // _playerTypeClasses[typeId] = styles;
        // playerTypeClass = styles;
      }
      return style;
    }

    function GetInteresseForPosition(interestId, styles) {
      switch (interestId) {
        case 1: //'Interesse-spelverdeler':
          styles.background = '#d9534f'
          styles['font-style'] = 'italic';
          break;
        case 2: // 'Interesse-midden':
          styles.background = '#5cb85c';
          styles['font-style'] = 'italic';
          break;
        case 3: //'Interesse-passer-loper':
          styles.background = '#f0ad4e';
          styles['font-style'] = 'italic';
          break;
        case 4: //'Interesse-diagonaal':
          styles.background = '#1bc5f8';
          styles['font-style'] = 'italic';
          break;
        case 5: //'Interesse-libero':
          styles.background ='#337ab7';
          styles['font-style'] = 'italic';
          break;
        case 6: // Interesse-niets
          styles.background = '#3b3333'
          styles['font-style'] = 'italic';
          break;
        case 7: //'Interesse-trainingslid':
          styles.background = '#8a8888fc'
          styles.border = 'border 1px solid #808080'
          styles['font-style'] = 'italic';
            break;
        case 8: //'Geen':
          return styles;
  
        default:
          return styles;
      }
      return styles
    }

    function GetStyleForPosition(typeId, interesseId) {
      if (typeof typeId === 'string') typeId = parseInt(typeId, 10);
      if (typeof interesseId === 'string') interesseId = parseInt(interesseId, 10)
      
      styles = GetClassForPosition(typeId);
      styles = GetInteresseForPosition(interesseId, styles);
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
