class CampaignplanningsEditController{
    constructor($scope, $stateParams, $state, $compile, DTOptionsBuilder, DTColumnBuilder, API, $log, $filter, MultipartFormService, ContextService){
        'ngInject';

        this.$state = $state;
        this.API = API;
        this.formSubmitted = false;
        this.alerts = [];
        this.$filter = $filter;
        this.$log = $log;
        this.mindate = new Date();
        this.MultipartFormService = MultipartFormService;

        if ($stateParams.alerts) {
            this.alerts.push($stateParams.alerts)
        }

        let planningId = $stateParams.planningId
        let Campaignplanning = API.service('campaignplannings-show', API.all('campaigns'))
        Campaignplanning.one(planningId).get()
            .then((response) => {

                this.planning = API.copy(response);

                $log.log('Campaignplannings show:',this.planning);
            })


        let Planningsent = this.API.service('planningsents-show', this.API.all('campaigns'))

        Planningsent.one(planningId).get()
            .then((response) => {
                $log.log('planningsents show:',response);
                let dataSet = response.data.planningsents;

                $log.log('planningsents dataSet:',dataSet);

                this.dtOptions = DTOptionsBuilder.newOptions()
                    .withOption('data', dataSet)
                    .withOption('createdRow', createdRow)
                    .withOption('responsive', true)
                    .withOption('autoWidth', false)
                    .withBootstrap()
                    .withLanguage({
                        "sEmptyTable":     "Aucune donnée disponible",
                        "sInfo":           "Affichage de _START_ à _END_ de _TOTAL_ entrées",
                        "sInfoEmpty":      "Affichage de 0 à 0 de 0 entrées",
                        "sInfoFiltered":   "(filtré à partir de _MAX_ entrées)",
                        "sInfoPostFix":    "",
                        "sInfoThousands":  ",",
                        "sLengthMenu":     "Afficher _MENU_ entrées",
                        "sLoadingRecords": "En cours de chargement...",
                        "sProcessing":     "Traitement en cours...",
                        "sSearch":         "Recherche:",
                        "sZeroRecords":    "Aucun enregistrement trouvé",
                        "oPaginate": {
                            "sFirst":    "Premier",
                            "sLast":     "Dernier",
                            "sNext":     "Suivant",
                            "sPrevious": "Précédent"
                        },
                        "oAria": {
                            "sSortAscending":  ": activate to sort column ascending",
                            "sSortDescending": ": activate to sort column descending"
                        }
                    })

                this.dtColumns = [
                    DTColumnBuilder.newColumn('id').withTitle('ID'),
                    DTColumnBuilder.newColumn('msg').withTitle('Message'),
                    DTColumnBuilder.newColumn('receiver').withTitle('Destinataire'),
                    DTColumnBuilder.newColumn('nbtry').withTitle('Tentatives'),
                    DTColumnBuilder.newColumn('resultstring').withTitle('Résultat'),
                    DTColumnBuilder.newColumn('start_at').withTitle('Début envoi'),
                    DTColumnBuilder.newColumn('end_at').withTitle('Fin envoi'),
                    DTColumnBuilder.newColumn(null).withTitle('Actions').notSortable()
                        .renderWith(actionsHtml)
                ]

                this.displayTable = true
            })

        let createdRow = (row) => {
            $compile(angular.element(row).contents())($scope)
        }

        let actionsHtml = (data) => {
            return `
                <a ng-show="false" class="btn btn-xs btn-warning" ui-sref="app.campaignplanningsedit({planningId: ${data.planning_id}})">
                    <i class="fa fa-edit"></i>
                </a>
                &nbsp
                <button ng-show="false" class="btn btn-xs btn-danger" ng-click="vm.planningdelete(${data.planning_id})">
                    <i class="fa fa-trash-o"></i>
                </button>`
        }

        let navHeader = this

        ContextService.me(function (data) {
            navHeader.userData = data
        })
    }

    $onInit(){
    }

    planningmodify (){
        let $log = this.$log;
        let $filter = this.$filter;
        let $state = this.$state;

        this.planning.data.plan_at = $filter('date')(this.planning.data.plan_at, "yyyy-MM-dd HH:mm:ss");

        this.planning.put()
            .then((response) => {
                $log.log('planning update response success',response);
                let alert = { type: 'success', 'title': 'Succès!', msg: 'Planification modifiée.' }
                $state.go($state.current, { alerts: alert})
            }, (response) => {
                $log.log('planning update response error',response);
                let alert = { type: 'error', 'title': 'Erreur!', msg: response.data.message }
                $state.go($state.current, { alerts: alert})
            });
    }

    planningduplicate (){
        let $log = this.$log;
        let $filter = this.$filter;
        let $state = this.$state;

        // 1. new planning data
        let newplanning_data = {
            id: null,
            campaign_id: this.planning.data.campaign.id,
            plan_at: $filter('date')(this.planning.data.plan_at, "yyyy-MM-dd HH:mm:ss"),
            user: angular.toJson(this.planning.data.user),
            receivers_fileid: this.planning.data.receivers_fileid,
            result: this.planning.data.result,
            status: this.planning.data.status,
            plan_status: 0
        };

        let Campaignplannings = this.API.service('campaignplannings', this.API.all('campaigns'));

        Campaignplannings.post(newplanning_data)
            .then(function (respdata) {
                let alert = { type: 'success', 'title': 'Succès!', msg: 'Planification dupliquée.' }
                $state.go($state.current, { alerts: alert})
                $log.log('new planning duplicated', respdata);
            }, function (respdata) {
                $log.log('error planning duplicate', respdata);
                let alert = { type: 'error', 'title': 'Erreur!', msg: response.data.message }
                $state.go($state.current, { alerts: alert})
            });
    }
}

export const CampaignplanningsEditComponent = {
    templateUrl: './views/app/components/campaignplannings-edit/campaignplannings-edit.component.html',
    controller: CampaignplanningsEditController,
    controllerAs: 'vm',
    bindings: {}
}


