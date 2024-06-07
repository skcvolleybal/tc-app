(function (app) {
   app.service('changeService', function (IOService, Notification, utilityService, $q, dataLists) {
      var changeTypes = {
         addPlayer: "addPlayer",
         updatePlayer: "updatePlayer",
         deletePlayer: "deletePlayer",

         addTeam: "addTeam",
         updateTeam: "updateTeam",
         deleteTeam: "deleteTeam"
      };

      function isTeamType(change) {
         return change.changeType.indexOf("Team") >= 0;
      }

      function isPlayerType(change) {
         return change.changeType.indexOf("Player") >= 0;
      }

      var _changes = [];
      var lastRefreshTimestamp = new Date();

      function SaveChange(change) {
         var newChange = {};
         if (isTeamType(change)) {
            newChange = {
               changeType: change.changeType,
               id: change.id,
               name: change.name,
               sequence: change.sequence,
               type: change.type
            };
         }
         else if (isPlayerType(change)) {
            newChange = {
               changeType: change.changeType,
               id: change.id,
               name: change.name,
               teamId: change.teamId,
               trainingId: change.trainingId,
               typeId: change.typeId
            };
         }
         else {
            throw "Type wijziging is niet mogelijk, type: '" + change.type + "'";
         }
         _changes.push(newChange);
      }

      function GetChangeType(change) {
         if (isPlayerType(change)) {
            return "player";
         }
         else if (isTeamType(change)) {
            return "team";
         }
         else {
            throw "Unknown change type:" + change.changeType;
         }
      }

      function SaveChanges() {
         console.log(_changes);
         var deferrable = $q.defer();
         if (_changes.length > 0) {
            IOService.executeAction('SaveChanges', { changes: _changes })
               .then(function (data) {
                  _changes = [];
                  deferrable.resolve(data);
      
                  // Add your console.log callback here
                  console.log('Changes saved successfully:', data);
               })
               .catch(function (error) {
                  // Handle any errors
                  console.error('Error saving changes:', error);
                  deferrable.reject(error);
               });
         }
         else {
            Notification.success("Geen wijzigingen");
         }
      
         return deferrable.promise;
      }
      

      function GetChanges() {
         var defer = $q.defer();
         var timestamp = lastRefreshTimestamp.getFullYear() + "-"
            + (lastRefreshTimestamp.getMonth() + 1) + "-"
            + lastRefreshTimestamp.getDate() + " "
            + lastRefreshTimestamp.getHours() + ":"
            + lastRefreshTimestamp.getMinutes() + ":"
            + lastRefreshTimestamp.getSeconds() + "."
            + lastRefreshTimestamp.getMilliseconds();
         lastRefreshTimestamp = new Date();
         IOService.executeAction('GetUpdatedPlayers', { timestamp: timestamp })
            .then(function (result) {
               var changeList = result.changes;
               if (changeList.length == 0) {
                  Notification.success("Er zijn geen wijzigingen");
               }
               else {
                  var newChanges = [];
                  for (var i = 0; i < changeList.length; i++) {
                     var playerId = changeList[i].id;
                     var isChangeInChangesLists = utilityService.FirstOrDefault(_changes, function (change) { return change.id == playerId && isPlayerType(change) }) == null;
                     if (isChangeInChangesLists) {
                        newChanges.push(changeList[i]);
                     }
                     else {
                        Notification.warning(changeList[i].name + " is op de server en in de browser gewijzigd en is niet ge�pdatet");
                     }
                  }
                  defer.resolve(newChanges);
               }
            });

         return defer.promise;
      }

      return {
         SaveChange: SaveChange,
         SaveChanges: SaveChanges,
         GetChanges: GetChanges,
         changeTypes: changeTypes
      };
   })
})(angular.module('tcApp'))