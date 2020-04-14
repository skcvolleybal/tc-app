(function (app) {
   app.controller('mainController', function (
      $scope, loginService, ngDialog, $timeout, $window, changeService, playerService, IOService, utilityService, dataLists, Notification, dragDropService
   ) {
      $scope.BindCtrlFunctions = function () {
         $($window).bind('keydown', function (event) {
            if (event.ctrlKey || event.metaKey) {
               switch (String.fromCharCode(event.which).toLowerCase()) {
                  case 'r':
                     event.preventDefault();
                     $scope.Refresh();
                     break;
                  case 's':
                     event.preventDefault();
                     $scope.SaveChanges();
                     break;
                  case 'l':
                     event.preventDefault();
                     $timeout(function () {
                        $scope.isLegendaVisible = !$scope.isLegendaVisible;
                     }, 0);
                     break;
                  case 'f':
                     event.preventDefault();
                     $scope.openSearchDialog();
                     break;
               }
            }
         });
      }
      $scope.BindCtrlFunctions();

      $scope.addTeam = function () {
         ngDialog.open({
            template: 'controllers/addTeam/addTeam.html',
            controller: 'addTeamController',
            showClose: false
         });
      };

      $scope.addPlayer = function () {
         ngDialog.open({
            template: 'controllers/addPlayer/addPlayer.html',
            controller: 'addPlayerController',
            showClose: false
         });
      };

      $scope.SaveChanges = function () {
         changeService.SaveChanges()
            .then(function (ids) {
               playerService.UpdateIds(ids.playerIds);
               dataLists.UpdateIds(ids.teamIds);
            });
      }

      $scope.Refresh = function () {
         changeService.GetChanges()
            .then(function (changes) {
               angular.forEach(changes, function (change) {
                  playerService.ChangePlayer(change);
               });
            });
      }

      $scope.Export = function () {
         IOService.executeAction('GetExcelExport');
      }

      $scope.SetOrder = function () {
         ngDialog.open({
            template: 'controllers/editTeamOrder/editTeamOrder.html',
            controller: 'editTeamOrderController',
            showClose: false,
            data: {
               isTeamsActive: $scope.isTeamsActive
            },
            onOpenCallback: function () {
               $(function () {
                  $("#sortableList").sortable();
               });
            }
         });
      }

      $scope.openSearchDialog = function () {
         $scope.isSearchBarOpen = !$scope.isSearchBarOpen;
         $timeout(function () {
            document.getElementById("searchBox").focus();
         }, 0);
      }

      $scope.OpenTrainingScheduleDialog = function () {
         var teamType = $scope.isTeamsActive ? 'team' : 'training';
         IOService.executeAction('GetTrainingInfo', { teamType: teamType })
            .then(function (result) {
               ngDialog.open({
                  template: 'controllers/editTrainingSchedule/editTrainingSchedule.html',
                  controller: 'editTrainingScheduleController',
                  showClose: false,
                  data: {
                     isTeamsActive: $scope.isTeamsActive,
                     teams: result.teams
                  }
               });
            });
      }

      $scope.login = function () {
         loginService.openloginDialog();
      }

      $scope.isTeamsActive = true;
      $scope.isLegendaVisible = false;
      $scope.loginService = loginService;
      dragDropService.SetTooltips();
   })
})(angular.module('tcApp'));