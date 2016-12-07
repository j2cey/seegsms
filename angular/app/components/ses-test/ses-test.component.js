class SesTestController{
    constructor ($scope, $state, $compile, DTOptionsBuilder, DTColumnBuilder, API) {
        'ngInject'
        this.API = API
        this.$state = $state

        let VarTests = this.API.service('tests')

        VarTests.getList()
            .then((response) => {
                let dataSet = response.plain()

                this.dtOptions = DTOptionsBuilder.newOptions()
                    .withOption('data', dataSet)
                    .withOption('createdRow', createdRow)
                    .withOption('responsive', true)
                    .withBootstrap()

                this.dtColumns = [
                    DTColumnBuilder.newColumn('id').withTitle('ID'),
                    DTColumnBuilder.newColumn('field1').withTitle('field1'),
                    DTColumnBuilder.newColumn('field2').withTitle('field2'),
                    DTColumnBuilder.newColumn(null).withTitle('Actions').notSortable()
                        .renderWith(actionsHtml)
                ]

                this.displayTable = true
            })

        let createdRow = (row) => {
            $compile(angular.element(row).contents())($scope)
        }

        let actionsHtml = () => {
            return `
                <a class="btn btn-xs btn-warning" ui-sref="#">
                    <i class="fa fa-edit"></i>
                </a>
                &nbsp
                <button class="btn btn-xs btn-danger" ng-click="#">
                    <i class="fa fa-trash-o"></i>
                </button>`
        }
    }

    $onInit(){
    }
}

export const SesTestComponent = {
    templateUrl: './views/app/components/ses-test/ses-test.component.html',
    controller: SesTestController,
    controllerAs: 'vm',
    bindings: {}
}


