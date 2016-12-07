class TraceEditController{
    constructor ($log, $stateParams, $scope, $state, $compile, DTOptionsBuilder, DTColumnBuilder, API) {
        'ngInject'

        this.$state = $state
        this.formSubmitted = false
        this.alerts = []

        if ($stateParams.alerts) {
            this.alerts.push($stateParams.alerts)
        }

        let traceId = $stateParams.traceId
        let Trace = API.service('tracesteps-show', API.all('traces'))
        Trace.one(traceId).get()
            .then((response) => {

                $log.log('reponse list', response);

                let dataSet = response.data.steps;
                this.trace = response.data.trace;

                $log.log('trace', this.trace);
                $log.log('data set', dataSet);

                this.dtOptions = DTOptionsBuilder.newOptions()
                    .withOption('data', dataSet)
                    .withOption('createdRow', createdRow)
                    .withOption('responsive', true)
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
                    DTColumnBuilder.newColumn('title').withTitle('Titre'),
                    DTColumnBuilder.newColumn('start_at').withTitle('Début'),
                    DTColumnBuilder.newColumn('end_at').withTitle('Fin'),
                    DTColumnBuilder.newColumn('time').withTitle('Durée'),
                    DTColumnBuilder.newColumn('exestring').withTitle('Text exécution'),
                    DTColumnBuilder.newColumn('result').withTitle('Résultat')/*,
                    DTColumnBuilder.newColumn(null).withTitle('Actions').notSortable()
                        .renderWith(actionsHtml)*/
                ]

                this.displayTable = true
            })

        let createdRow = (row) => {
            $compile(angular.element(row).contents())($scope)
        }

        /*let actionsHtml = (data) => {
            return `
                <a class="btn btn-xs btn-warning" ui-sref="app.traceedit({traceId: ${data.id}})">
                    <i class="fa fa-edit"></i>
                </a>`
        }*/
    }

    $onInit(){
    }
}

export const TraceEditComponent = {
    templateUrl: './views/app/components/trace-edit/trace-edit.component.html',
    controller: TraceEditController,
    controllerAs: 'vm',
    bindings: {}
}


