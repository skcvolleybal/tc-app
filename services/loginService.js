(function (app) {
   app.service('loginService', function (IOService, ngDialog, dataLists) {
      var _isLoggedIn = false;

      function isLoggedIn() {
         return _isLoggedIn;
      }

      function setLoggedIn(loggedIn) {
         _isLoggedIn = loggedIn;
      }

      function openloginDialog() {
         ngDialog.open({
            template: 'controllers/login/login.html',
            controller: 'loginController',
            showClose: false
         });
      };

      function checkIfuserIsLoggedIn() {
         IOService.executeAction('CheckIfuserIsLoggedIn')
            .then(function (result) {
               if (result.userIsloggedIn === true) {
                  dataLists.SetLists(result.dataLists);
                  _isLoggedIn = true;
               }
               else {
                  openloginDialog();
               }
            })
      }

      checkIfuserIsLoggedIn();

      return {
         isLoggedIn: isLoggedIn,
         openloginDialog: openloginDialog,
         checkIfuserIsLoggedIn: checkIfuserIsLoggedIn,
         setLoggedIn: setLoggedIn
      }
   });
})(angular.module('tcApp'));