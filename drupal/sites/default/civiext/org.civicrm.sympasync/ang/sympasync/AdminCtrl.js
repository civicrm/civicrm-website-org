(function(angular, $, _) {

  angular.module('sympasync').config(function($routeProvider) {
      $routeProvider.when('/sympa/admin', {
        controller: 'SympasyncAdminCtrl',
        templateUrl: '~/sympasync/AdminCtrl.html',
        resolve: {
          staticGroups: function(crmApi) {
            return crmApi('Group', 'get', {
              'option.limit': 0,
              is_active: 1,
              saved_search_id: {'IS NULL': 1},
              return: 'title'
            });
          },
          sqlCred: function(crmApi) {
            return crmApi('Sympasync', 'getcred', {});
          }
        }
      });
    }
  );

  angular.module('sympasync').controller('SympasyncAdminCtrl', function($scope, crmApi, crmStatus, crmUiHelp, sqlCred, staticGroups) {
    var ts = $scope.ts = CRM.ts('sympasync');
    var hs = $scope.hs = crmUiHelp({file: 'CRM/sympasync/AdminCtrl'}); // See: templates/CRM/sympasync/AdminCtrl.hlp

    $scope.sqlCred = sqlCred.values;
    $scope.sqlView = 'sympa_subscribers';
    $scope.selected = {groupId: null};
    $scope.staticGroups = staticGroups.values;
    $scope.unsubscribeBaseUrl = CRM.url('civicrm/sympa/unsubscribe');
    if ($scope.unsubscribeBaseUrl[0] === '/') {
      $scope.unsubscribeBaseUrl = window.location.protocol + '//' + window.location.host + $scope.unsubscribeBaseUrl;
    }
  });

})(angular, CRM.$, CRM._);
