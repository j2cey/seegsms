class CampaignsListController{
    constructor ($scope, $state, $compile, DTOptionsBuilder, DTColumnBuilder, API, $log) {
        'ngInject'
        this.API = API
        this.$state = $state

        let Campaigns = this.API.service('campaigns', this.API.all('campaigns'))

        Campaigns.getList()
            .then((response) => {
                $log.log('campagne list:',response);
                let dataSet = response.plain();
                $log.log('campagne dataSet:',dataSet);

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
                    /*.withButtons([
                        'columnsToggle',
                        'colvis',
                        'copy',
                        'pdf',
                        'excel',
                        {
                            text: 'Some button',
                            key: '1',
                            action: function (e, dt, node, config) {
                                alert('Button activated');
                            }
                        }
                    ])*/
                    .withButtons([
                        {
                            extend: "excelHtml5",
                            filename:  "Data_Analysis",
                            title:"Data Analysis Report",
                            exportOptions: {
                                columns: ':visible'
                            },
                            //CharSet: "utf8",
                            exportData: { decodeEntities: true }
                        },
                        {
                            extend: "csvHtml5",
                            fileName:  "Data_Analysis",
                            exportOptions: {
                                columns: ':visible'
                            },
                            exportData: {decodeEntities:true}
                        },
                        {
                            extend: "pdfHtml5",
                            fileName:  "Data_Analysis",
                            title:"Data Analysis Report",
                            exportOptions: {
                                columns: ':visible'
                            },
                            exportData: {decodeEntities:true}
                        },
                        {
                            extend: 'print',
                            //text: 'Print current page',
                            autoPrint: false,
                            exportOptions: {
                                columns: ':visible'
                            }
                        }
                    ])

                this.dtColumns = [
                    DTColumnBuilder.newColumn('campaign_id').withTitle('ID'),
                    DTColumnBuilder.newColumn('campaign_title').withTitle('Titre'),
                    DTColumnBuilder.newColumn('campaign_msg').withTitle('Message'),
                    //DTColumnBuilder.newColumn('lastName').withTitle('Last name').withClass('none')
                    DTColumnBuilder.newColumn('campaign_descript').withTitle('Description').withClass('none'),
                    DTColumnBuilder.newColumn('campaigntype').withTitle('Type').withClass('none'),
                    //DTColumnBuilder.newColumn('campaign_status').withTitle('Statut').withClass('none'),
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
                <a class="btn btn-xs btn-warning" ui-sref="app.campaignsedit({campaignId: ${data.campaign_id}})">
                    <i class="fa fa-edit"></i>
                </a>
                &nbsp
                <button class="btn btn-xs btn-danger" ng-click="vm.delete(${data.campaign_id})">
                    <i class="fa fa-trash-o"></i>
                </button>`
        }
    }

    delete (campaignId) {
        let API = this.API
        let $state = this.$state

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
            API.one('campaigns').one('campaigns', campaignId).remove()
                .then(() => {
                    swal({
                        title: 'Supprimé!',
                        text: 'Campagne supprimé.',
                        type: 'success',
                        confirmButtonText: 'OK',
                        closeOnConfirm: true
                    }, function () {
                        $state.reload()
                    })
                })
        })
    }

    $onInit(){
    }
}

export const CampaignsListComponent = {
    templateUrl: './views/app/components/campaigns-list/campaigns-list.component.html',
    controller: CampaignsListController,
    controllerAs: 'vm',
    bindings: {}
}


