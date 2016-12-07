class CampaignsEditController{
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

        let campaignId = $stateParams.campaignId
        let Campaign = API.service('campaigns-show', API.all('campaigns'))
        Campaign.one(campaignId).get()
            .then((response) => {

                this.campaign = API.copy(response);

                $log.log('campagne show:',this.campaign);
            })

        let Planningscampaign = this.API.service('planningscampaign-show', this.API.all('campaigns'))

        Planningscampaign.one(campaignId).get()
            .then((response) => {
                $log.log('planningscampaign show:',response);
                let dataSet = response.data.planningscampaign;

                $log.log('planningscampaign dataSet:',dataSet);

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
                    DTColumnBuilder.newColumn('planning_id').withTitle('ID'),
                    DTColumnBuilder.newColumn('toplandate').withTitle('Date planification'),
                    DTColumnBuilder.newColumn('planning_statusstring').withTitle('Statut'),
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
                <a class="btn btn-xs btn-warning" ui-sref="app.campaignplanningsedit({planningId: ${data.planning_id}})">
                    <i class="fa fa-edit"></i>
                </a>
                &nbsp
                <button ng-show="${data.planning_status} == 1" class="btn btn-xs btn-danger" ng-click="vm.planningdelete(${data.planning_id})">
                    <i class="fa fa-trash-o"></i>
                </button>`
        }

        let navHeader = this

        ContextService.me(function (data) {
            navHeader.userData = data
        })
    }

    save (isValid) {
        if (isValid) {
            let $state = this.$state;
            let $log = this.$log;

            /*$log.log('campagne befor plandate formate:',this.campaign);*/

            //this.campaign.data.planning.plan_at = $filter('date')(this.campaign.data.planning.plan_at, "yyyy-MM-dd HH:mm:ss");

            /*angular.forEach(this.campaign.data.plannings, function (value, key) {
                value.plan_at = $filter('date')(value.plan_at, "yyyy-MM-dd HH:mm:ss");
            });*/

            /*angular.forEach(this.campaign.data.plannings, function(planning, key, plannings) {

                //thing.x += 10;         // <--- works
                //thing.k = thing.x + 1; // <--- works
                //thing = {k: thing.x + 10, o: thing.x - 1};  // <--- doesn't work

                //things[key] = {k: thing.x + 10, o: thing.x - 1}; // <--- works!

                planning.plan_at = $filter('date')(planning.plan_at, "yyyy-MM-dd HH:mm:ss");

            });*/

            /*$log.log('campagne after plandate formate',this.campaign);*/

            this.campaign.put()
                .then((response) => {
                    $log.log('response success',response);
                    let alert = { type: 'success', 'title': 'Succès!', msg: 'Campagne modifiée.' }
                    $state.go($state.current, { alerts: alert})
                }, (response) => {
                    $log.log('response error',response);
                    let alert = { type: 'error', 'title': 'Erreur!', msg: response.data.message }
                    $state.go($state.current, { alerts: alert})
                })
        } else {
            this.formSubmitted = true
        }
    }

    planningdelete (planningId) {
        let API = this.API;
        let $state = this.$state;
        let deldatatmp = {};
        let deldata = {};
        let $log = this.$log;

        deldatatmp.user = angular.toJson(this.userData);
        deldatatmp.planningId = planningId;
        deldata = angular.toJson(deldatatmp);

        swal({
            title: 'Êtes-vous sûr?',
            text: 'Vous ne pourriez plus récupérer ces données!',
            type: 'warning',
            showCancelButton: true,
            cancelButtonText: 'Annuler',
            confirmButtonColor: '#DD6B55',
            confirmButtonText: 'Oui, supprimer!',
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
            html: false
        }, function () {
            API.one('campaigns').one('campaignplannings', deldata).remove()
                .then((response) => {
                    $log.log('del campaignplannings response:',response);
                    swal({
                        title: 'Supprimé!',
                        text: 'Planification supprimée.',
                        type: 'success',
                        confirmButtonText: 'OK',
                        closeOnConfirm: true
                    }, function () {
                        $state.reload()
                    })
                })
        })
    }


    planningcancel (){
        this.campaign.newplandate = null;
        this.campaign.receiversfile = null;
        this.campaign.planningmodif = null;

        angular.element("input[type='file']").val(null);

        /*angular.forEach(
            angular.element("input[type='file']"),
            function(inputElem) {
                angular.element(inputElem).val(null);
            });*/
    }

    planningadd (plandate, plannings){
        let $log = this.$log;
        let $filter = this.$filter;
        var urlApi = '/api/campaigns/campaignplannings';
        let newplanning = {};

        // 1. new planning data
        let newplanning_data = {
            campaign_id: this.campaign.data.id,
            plan_at: $filter('date')(plandate, "yyyy-MM-dd HH:mm:ss"),
            user: angular.toJson(this.campaign.data.user),
            receivers_fileid: null
        };

        // 2. get file
        var files = {'receiversfile':this.campaign.receiversfile};

        // 3. post multi-part form
        var response = this.MultipartFormService.uploadForm(urlApi,newplanning_data,files);

        response.then(function (respdata) {
            $log.log('new planning posted', respdata);
            newplanning = respdata.data.data.campaignplanning;
            plannings.push(newplanning);
        }, function (respdata) {
            $log.log('error new planning post', respdata);
        });

        $log.log('plannings after add',this.campaign.data.plannings);
    }

    planningmodifyaffect (planning){
        this.planningcancel();
        this.campaign.newplandate = planning.plan_at;
        this.campaign.planningmodif = planning;
    }

    planningmodify (planning, plandate){
        let $filter = this.$filter;
        let $newplannig = planning;

        // 1. modify new planning date
        $newplannig.plan_at = $filter('date')(plandate, "yyyy-MM-dd HH:mm:ss");

        // 2. replace old planning
        this.planningreplace(planning,$newplannig);
    }

    planningduplicate (planning, plandate, plannings){
        let $log = this.$log;
        let $filter = this.$filter;
        let newplanning = {};

        // 1. new planning data
        let newplanning_data = {
            id: null,
            campaign_id: this.campaign.data.id,
            plan_at: $filter('date')(plandate, "yyyy-MM-dd HH:mm:ss"),
            user: angular.toJson(this.campaign.data.user),
            receivers_fileid: planning.receivers_fileid,
            result: planning.result,
            status: planning.status,
            plan_status: 0
        };

        let Campaignplannings = this.API.service('campaignplannings', this.API.all('campaigns'));

        Campaignplannings.post(newplanning_data)
            .then(function (respdata) {
                $log.log('new planning duplicated', respdata);
                newplanning = respdata.data.campaignplanning;
                plannings.push(newplanning);
            }, function (respdata) {
                $log.log('error planning duplicate', respdata);
            });

        $log.log('plannings after duplicate',this.campaign.data.plannings);
    }

    planningdrop (planning){
        let $log = this.$log;

        // 1. get curr planning index
        let $planningindex = this.campaign.data.plannings.indexOf(planning);

        // 2. delete curr planning from list
        this.campaign.data.plannings.splice($planningindex, 1);

        // 3. add planning to drop list
        this.campaign.data.plannings_drop.push(planning);

        $log.log('plannings after drop',this.campaign.data.plannings);
        $log.log('plannings drop list',this.campaign.data.plannings_drop);
    }


    planningreplace (oldplanning, newplanning){
        let $log = this.$log;

        // 1. get old planning index
        let $planningindex = this.campaign.data.plannings.indexOf(oldplanning);
        this.campaign.data.plannings.splice($planningindex, 1);

        // 2. delete old planning from list
        this.campaign.data.plannings.splice($planningindex, 1);

        // 3. add new planning to planning list
        this.campaign.data.plannings.push(newplanning);

        $log.log('plannings after replace',this.campaign.data.plannings);
    }

    $onInit(){
    }
}

export const CampaignsEditComponent = {
    templateUrl: './views/app/components/campaigns-edit/campaigns-edit.component.html',
    controller: CampaignsEditController,
    controllerAs: 'vm',
    bindings: {}
}


