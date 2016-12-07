class TraceListsController{
    constructor($log, $scope, $state, $compile, DTOptionsBuilder, DTColumnBuilder, API) {
        'ngInject'
        this.API = API
        this.$state = $state

        let Trace = this.API.service('traces')

        Trace.getList()
            .then((response) => {

                $log.log(response);

                let dataSet = response.plain()

                $log.log(dataSet);

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
                    DTColumnBuilder.newColumn('user').withTitle('Acteur'),
                    DTColumnBuilder.newColumn('request').withTitle('Requete'),
                    DTColumnBuilder.newColumn('status').withTitle('Statut'),
                    DTColumnBuilder.newColumn('result').withTitle('Resultat'),
                    DTColumnBuilder.newColumn('start_at').withTitle('Début'),
                    DTColumnBuilder.newColumn('end_at').withTitle('Fin'),
                    DTColumnBuilder.newColumn('time').withTitle('Durée'),
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
                <a class="btn btn-xs btn-warning" ui-sref="app.traceedit({traceId: ${data.id}})">
                    <i class="fa fa-edit"></i>
                </a>`
        }
    }

    $onInit(){
    }
}

export const TraceListsComponent = {
    templateUrl: './views/app/components/trace-lists/trace-lists.component.html',
    controller: TraceListsController,
    controllerAs: 'vm',
    bindings: {}
}


