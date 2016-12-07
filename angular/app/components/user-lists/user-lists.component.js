class UserListsController {
  constructor ($scope, $state, $compile, DTOptionsBuilder, DTColumnBuilder, API) {
    'ngInject'
    this.API = API
    this.$state = $state

    let Users = this.API.service('users')

    Users.getList()
      .then((response) => {
        let dataSet = response.plain()

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
          DTColumnBuilder.newColumn('name').withTitle('Nom'),
          DTColumnBuilder.newColumn('email').withTitle('Email'),
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
                <a class="btn btn-xs btn-warning" ui-sref="app.useredit({userId: ${data.id}})">
                    <i class="fa fa-edit"></i>
                </a>
                &nbsp
                <button class="btn btn-xs btn-danger" ng-click="vm.delete(${data.id})">
                    <i class="fa fa-trash-o"></i>
                </button>`
    }
  }

  delete (userId) {
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
      API.one('users').one('user', userId).remove()
        .then(() => {
          swal({
            title: 'Supprimé!',
            text: 'L\' utilisateur a été supprimé.',
            type: 'success',
            confirmButtonText: 'OK',
            closeOnConfirm: true
          }, function () {
            $state.reload()
          })
        })
    })
  }

  $onInit () {}
}

export const UserListsComponent = {
  templateUrl: './views/app/components/user-lists/user-lists.component.html',
  controller: UserListsController,
  controllerAs: 'vm',
  bindings: {}
}
