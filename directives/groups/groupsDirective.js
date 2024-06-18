(function (app) {
  app.directive('groups', function (playerService) {
    return {
      templateUrl: 'directives/groups/groups.html',
      controller: function (
        $scope,
        $element,
        playerService,
        changeService,
        dataLists,
        utilityService,
        dragDropService
      ) {
        $scope.playerService = playerService;
        $scope.dataLists = dataLists;
        $scope.GetClassForPosition = utilityService.GetClassForPosition;
        $scope.GetStyleForPosition = utilityService.GetStyleForPosition;
        $scope.GetInteresseForPosition = utilityService.GetInteresseForPosition;

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

        $scope.ChangeInteresse = function (player, newType) {
          console.log("ChangeInteresse " + newType.id)
          player.interesseId = newType.id;
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

        function getCookieValue(name) {
          const regex = new RegExp(`(^| )${name}=([^;]+)`)
          const match = document.cookie.match(regex)
          if (match) {
            return match[2]
          }
        }

        // New function to toggle visibility and set cookie
        $scope.toggleVisibility = function (team) {
          team.isVisible = !team.isVisible; // Toggle the visibility

          var visibleTeams = getCookieValue("visibleTeams");
          if (!visibleTeams) {
            visibleTeams = [];
          } else {
            visibleTeams = visibleTeams.split(',');  
          }

          var index = visibleTeams.indexOf(team.name);
          if (team.isVisible) {
            // If the team is now visible and not in the list, add it
            if (index === -1) {
              visibleTeams.push(team.name);
            }
          } else {
            // If the team is now not visible and is in the list, remove it
            if (index !== -1) {
              visibleTeams.splice(index, 1);
            }
          }

          // Save the updated array back to the cookie
          setCookieValue("visibleTeams", visibleTeams.join(','), 365); 
        };

        function setCookieValue(name, value, days) {
          var expires = "";
          if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
          }
          document.cookie = name + "=" + (value || "") + expires + "; path=/";
        }

        // Event handler for toggling submenu visibility
          // $element.find('.dropdown-submenu > a.test').on("click", function (e) {
          //   console.log("hier");
          //   var $parentLi = $(this).parent('.dropdown-submenu');
          //   console.log($parentLi);
          //   $parentLi.toggleClass('open');
          //   $parentLi.find('ul.dropdown-menu').toggle();
          //   e.stopPropagation();
          //   e.preventDefault();
          // });

        // Initialize the visibleTeams cookie if it doesn't exist
        var visibleTeams = getCookieValue("visibleTeams");
        if (!visibleTeams) {
          setCookieValue("visibleTeams", ",", 365);
        }


      },
    };
  });
})(angular.module('tcApp'));
