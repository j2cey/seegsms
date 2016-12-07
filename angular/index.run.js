import { RoutesRun } from './run/routes.run'

angular.module('app.run')
  .run(RoutesRun, function($rootScope) {
      $rootScope.planningtovalidate = 0;
  }
  )
