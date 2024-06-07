(function (app) {
   app.service('playerService', function (IOService, $rootScope, $q, dataLists, changeService, utilityService, Notification) {
      var mappings = [{
         name: "Teams",
         type: "team",
         data: []
      }, {
         name: "Trainingsgroepen",
         type: "training",
         data: []
      }];

      var idCounter = -1;

      function GetPlayersInTeam(players) {
         var result = [];
         angular.forEach(players, function (player) {
            result.push({
               id: player.id,
               name: player.name,
               typeId: player.typeId,
               teamId: player.teamId,
               trainingId: player.trainingId,
               information: player.information
            });
         });

         return result;
      }

      function OrderTeams(teamOrder, mappingType) {
         angular.forEach(mappings, function (mapping) {
            if (mapping.type === mappingType) {
               for (var i = 0; i < teamOrder.length; i++) {
                  var temp = mapping.data[i];
                  for (var j = i; j < teamOrder.length; j++) {
                     if (mapping.data[j].id == teamOrder[i].id) break;
                  }
                  mapping.data[i] = mapping.data[j];
                  mapping.data[j] = temp;
               }
            }
         });
      }

      function DeletePlayer(playerId) {
         angular.forEach(mappings, function (mapping) {
            angular.forEach(mapping.data, function (team) {
               for (var i = 0; i < team.players.length; i++) {
                  var player = team.players[i];
                  if (player.id == playerId) {
                     team.players.splice(i, 1);
                  }
               }
            })
         })
      }

      function PlacePlayerInTeam(team, newPlayer) {
         var newPosition = team.players.length;
         for (var i = 0; i < team.players.length; i++) {
            var player = team.players[i];
            if (player.typeId > newPlayer.typeId || player.typeId == newPlayer.typeId && player.name > newPlayer.name) {
               newPosition = i;
               break;
            }
         }

         var newListItem = {
            id: newPlayer.id,
            name: newPlayer.name,
            typeId: newPlayer.typeId,
            teamId: newPlayer.teamId,
            trainingId: newPlayer.trainingId,
            information: newPlayer.information
         };
         team.players.splice(newPosition, 0, newListItem);
      }

      function AddPlayerToMappings(newPlayer) {
         angular.forEach(mappings, function (mapping) {
            angular.forEach(mapping.data, function (team) {
               if (team.id == newPlayer.teamId) {
                  PlacePlayerInTeam(team, newPlayer);
               }
               if (team.id == newPlayer.trainingId) {
                  PlacePlayerInTeam(team, newPlayer);
               }
            });
         });
      }

      function ChangePlayer(newPlayer) {
         DeletePlayer(newPlayer.id);
         AddPlayerToMappings(newPlayer);

         ApplyChangesInUI();
      }

      function ApplyChangesInUI() {
         if ($rootScope.$$phase != '$apply' && $rootScope.$$phase != '$digest') {
            console.log("UI Updated");
            // Auto trigger save here?
            $rootScope.$applyAsync(function() {
               changeService.SaveChanges();
            });
         }
      }

      function LoadAllPlayers() {
         var deferred = $q.defer();
         mappings[0].data = [];
         mappings[1].data = [];

         IOService.executeAction('GetAllPlayers')
            .then(function (lists) {
               angular.forEach(lists.teamMapping, function (team) {
                  mappings[0].data.push({
                     id: team.id,
                     name: team.name,
                     sequence: team.sequence,
                     isVisible: false,
                     players: GetPlayersInTeam(team.players)
                  });
               });

               angular.forEach(lists.trainingGroupMapping, function (team) {
                  mappings[1].data.push({
                     id: team.id,
                     name: team.name,
                     sequence: team.sequence,
                     isVisible: false,
                     players: GetPlayersInTeam(team.players)
                  });
               });

               deferred.resolve();
            });
         return deferred.promise;
      }

      function AddPlayer(player) {
         var newId = idCounter--;
         player.id = newId;
         AddPlayerToMappings(player);
         return newId;
      }

      function GetPlayer(playerId) {
         return IOService.executeAction('GetPlayer', { playerId: playerId });
      }

      function GetNewSequence(mapping) {
         return "" + (parseInt(mapping.data[mapping.data.length - 1].sequence) + 1);
      }

      function AddTeam(team) {
         console.log(team);
         var newTeam = {};
         angular.forEach(mappings, function (mapping) {
            if (mapping.type === team.type) {
               var newSequence = GetNewSequence(mapping);
               var newId = idCounter--;
               newTeam = {
                  changeType: team.changeType,
                  id: newId,
                  name: team.name,
                  type: team.type,
                  sequence: newSequence,
                  isVisible: true,
                  players: []
               }
               mapping.data.push(newTeam);
            }
         });

         return newTeam;
      }

      function DeleteTeam(id) {
         angular.forEach(mappings, function (mapping) {
            for (var i = 0; i < mapping.data.length; i++) {
               if (mapping.data[i].id === id) {
                  mapping.data.splice(i, 1);
               }
            };
         })
      }

      function UpdateIds(ids) {
         angular.forEach(mappings, function (mapping) {
            angular.forEach(mapping.data, function (team) {
               angular.forEach(team.players, function (player) {
                  angular.forEach(ids, function (newPlayerId, oldPlayerId) {
                     if (player.id == oldPlayerId) {
                        player.id = newPlayerId;
                     }
                  })
               });
            });
         });
      }

      function SetInformation(id, information) {
         angular.forEach(mappings, function (mapping) {
            angular.forEach(mapping.data, function (team) {
               angular.forEach(team.players, function (player) {
                  if (player.id == id) {
                     player.information = information;
                  }
               });
            });
         });
      }

      return {
         GetPlayer: GetPlayer,
         AddPlayer: AddPlayer,
         LoadAllPlayers: LoadAllPlayers,
         mappings: mappings,
         ChangePlayer: ChangePlayer,
         DeletePlayer: DeletePlayer,
         AddTeam: AddTeam,
         DeleteTeam: DeleteTeam,
         OrderTeams: OrderTeams,
         UpdateIds: UpdateIds,
         SetInformation: SetInformation
      };
   })
})(angular.module('tcApp'))
