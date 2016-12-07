class DashboardController {
  constructor ($scope, API, $state, $log) {
    'ngInject'

    this.nbattentevalidation = 0;
    this.nbcourstraitement = 0;
    this.nbechecs = 0;
    this.nbattentefacture = 0;

    this.API = API;

    $scope.labels = ['Janvier', 'Fevrier', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'Decembre']
    $scope.series = ['Succes', 'Echecs']
    $scope.data = [
      [65, 59, 80, 81, 56, 55, 40, 59, 80, 81, 56, 55],
      [28, 48, 40, 19, 86, 27, 90, 48, 40, 19, 86, 27]
    ]

    $scope.lastcampaigns = [];
    let Lastcampaigns = this.API.service('lastcampaigns', this.API.all('campaigns'));
    Lastcampaigns.getList().then((response) => {

      $log.log('last campagnes response',response);

      $scope.lastcampaigns = []
      $scope.lastcampaigns = response.plain()
    })

    $scope.lastcampaignplannings = [];
    let Lastcampaignplannings = this.API.service('lastcampaignplannings', this.API.all('campaigns'));
    Lastcampaignplannings.getList().then((response) => {
      $scope.lastcampaignplannings = []
      $scope.lastcampaignplannings = response.plain()
    })

    $scope.onClick = function () {}

    $scope.pieLabels = ['Factures', 'Relances', 'Message ponctuel']
    $scope.pieData = [300, 500, 100]

    let CampaignvalidatingApi = API.service('planningvalidatings', this.API.all('dashboard'))
    CampaignvalidatingApi.getList()
        .then((response) => {
          $log.log('all validatings resp', response);
          let allvalidation = [];
          //allvalidation = response.data.planningvalidatings;
          allvalidation = response.plain();
          $log.log('all validatings plain', allvalidation);
          this.nbattentevalidation = allvalidation.length;
        }, function (respdata) {
          $log.log(respdata);
          let alert = { type: 'error', 'title': 'Erreur !', msg: respdata.statusText };
          $state.go($state.current, { alerts: alert})
        });

    let CampaignrunningApi = API.service('planningrunnings', this.API.all('dashboard'))
    CampaignrunningApi.getList()
        .then((response) => {
            let allrunning = [];
          $log.log('all allrunning resp', response);
          allrunning = response.plain();
          $log.log('all allrunning plain', allrunning);
          this.nbcourstraitement = allrunning.length;
        }, function (respdata) {
          $log.log(respdata);
          let alert = { type: 'error', 'title': 'Erreur !', msg: respdata.statusText };
          $state.go($state.current, { alerts: alert})
        });

    let CampaignfailedApi = API.service('planningfailed', this.API.all('dashboard'))
    CampaignfailedApi.getList()
        .then((response) => {
            let allfailed = [];
          $log.log('all allfailed resp', response);
          allfailed = response.plain();
          $log.log('all allfailed plain', allfailed);
          this.nbechecs = allfailed[0].pctgfailed;
        }, function (respdata) {
          $log.log(respdata);
          let alert = { type: 'error', 'title': 'Erreur !', msg: respdata.statusText };
          $state.go($state.current, { alerts: alert})
        });
  }
}

export const DashboardComponent = {
  templateUrl: './views/app/components/dashboard/dashboard.component.html',
  controller: DashboardController,
  controllerAs: 'vm',
  bindings: {}
}
