(function (app) {
   app.service('dataLists', function (IOService) {
      var dataLists = {};

      function SetLists(lists) {
         dataLists = lists;
      }

      function GetLists() {
         return dataLists;
      }

      function GetTeams() {
         return dataLists.teams;
      }

      function GetTrainingGroups() {
         return dataLists.trainingGroups;
      }

      function GetPlayerTypes() {
         return dataLists.playerTypes;
      }

      function AddTeam(team) {
         var newTeam = {
            id: team.id,
            type: team.type,
            name: team.name
         };

         if (team.type === 'team') {
            newTeam.sequence = parseInt(dataLists.teams[dataLists.teams.length - 1].sequence) + 1;
            dataLists.teams.push(newTeam);
         } else if (team.type === 'training') {
            newTeam.sequence = parseInt(dataLists.trainingGroups[dataLists.trainingGroups.length - 1].sequence) + 1;
            dataLists.trainingGroups.push(team);
         }
         else {
            throw 'Unknown type: ' + team.type;
         }

         console.log(dataLists);
      }

      function DeleteTeam(teamId) {
         for (var i = 0; i < dataLists.teams.length; i++) {
            if (dataLists.teams[i].id == teamId) {
               dataLists.teams.splice(i, 1);
               return;
            }
         }

         for (var i = 0; i < dataLists.trainingGroups.length; i++) {
            if (dataLists.trainingGroups[i].id == teamId) {
               dataLists.trainingGroups.splice(i, 1);
               return;
            }
         }
      }

      function UpdateIds(ids) {
         for (var i = 0; i < dataLists.teams.length; i++) {
            angular.forEach(ids, function (newTeamId, oldTeamId) {
               if (dataLists.teams.id == oldTeamId) {
                  dataLists.teams.id = newTeamId;
               }
            });
         }

         for (var i = 0; i < dataLists.trainingGroups.length; i++) {
            angular.forEach(ids, function (newTeamId, oldTeamId) {
               if (dataLists.trainingGroups.id == oldTeamId) {
                  dataLists.trainingGroups.id = newTeamId;
               }
            });
         }
      }

      function OrderTeams(teamOrder, teamType) {
         var teams;
         if (teamType === 'team') {
            teams = dataLists.teams;
         }
         else if (teamType === 'training') {
            teams = dataLists.trainingGroups;
         }
         else {
            throw "Unknown teamType '" + teamType + "' in dataLists.OrderTeams";
         }

         for (var i = 0; i < teams.length; i++) {
            var temp = teams[i];
            for (var j = i; j < teamOrder.length; j++) {
               if (teams[j].id == teamOrder[i].id) break;
            }
            teams[i] = teams[j];
            teams[j] = temp;
         }
      }

      return {
         GetTrainingGroups: GetTrainingGroups,
         GetTeams: GetTeams,
         GetPlayerTypes: GetPlayerTypes,
         GetLists: GetLists,
         SetLists: SetLists,
         AddTeam: AddTeam,
         DeleteTeam: DeleteTeam,
         UpdateIds: UpdateIds,
         OrderTeams: OrderTeams
      }
   })
})(angular.module('tcApp'));