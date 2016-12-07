class NavSidebarController {
  constructor (AclService, ContextService, API, $log, $rootScope) {
    'ngInject'

    let navSideBar = this
    this.can = AclService.can
    this.API = API;
    this.myuserdata = {};

    ContextService.me(function (data) {
        navSideBar.userData = data;
        /*this.myuserdata = data;*/

        /*let userdata = data;

      let Planningvalidating = API.service('planningvalidating-show', API.all('campaigns'));
      Planningvalidating.one(userdata.id).get()
          .then((response) => {
            let validatingdata = response.data.planningvalidating;
            this.campaigntovalidate = validatingdata.length;

          });*/
    });

      $log.log('sidebar user data:', navSideBar.userData);

    let Planningvalidating = API.service('planningvalidating-show', API.all('campaigns'));
    Planningvalidating.one(1).get()
        .then((response) => {
          let validatingdata = response.data.planningvalidating;
            $rootScope.planningtovalidate = validatingdata.length;

          $log.log('sidebar to validate', $rootScope.planningtovalidate);
        });

  }

  $onInit () {}

  gettovalidate (id){

    let Planningvalidating = this.API.service('planningvalidating-show', this.API.all('campaigns'));
    Planningvalidating.one(id).get()
        .then((response) => {
          let validatingdata = response.data.planningvalidating;
          this.campaigntovalidate = validatingdata.length;

        });

    return this.campaigntovalidate;
  }
}

export const NavSidebarComponent = {
  templateUrl: './views/app/components/nav-sidebar/nav-sidebar.component.html',
  controller: NavSidebarController,
  controllerAs: 'vm',
  bindings: {}
}
