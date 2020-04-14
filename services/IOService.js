(function (app) {
  app.service('IOService', function ($http, $q, Notification) {
    // var baseUrl = 'https://www.skcvolleybal.nl/tc-app/php/interface.php'
    var baseUrl = '/joomla-website/tc-app/php/interface.php';

    var executeAction = function (action, obj) {
      console.log(action);
      if (obj) {
        obj.action = action;
      } else {
        obj = {
          action: action,
        };
      }

      var deferred = $q.defer();

      $http.post(baseUrl, obj).then(
        function (result) {
          console.log(result);
          if (result.data && result.data.message) {
            Notification.success(result.data.message);
          }
          deferred.resolve(result.data);
        },
        function (result) {
          console.log('Error on action ' + action + ' with object:');
          console.log(obj);
          console.log('resulted in the following error:');
          console.log(result);
          Notification.error(result.data.errorMessage);
          deferred.reject(result);
        }
      );

      return deferred.promise;
    };

    return {
      executeAction: executeAction,
    };
  });
})(angular.module('tcApp'));
