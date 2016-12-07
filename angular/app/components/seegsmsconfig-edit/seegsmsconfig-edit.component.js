class SeegsmsconfigEditController{
    constructor(AclService, $stateParams, $state, API, $log){
        'ngInject';

        this.$state = $state;
        this.API = API;
        this.formSubmitted = false;
        this.alerts = [];
        this.$log = $log;
        this.can = AclService.can;

        if ($stateParams.alerts) {
            this.alerts.push($stateParams.alerts)
        }

        let Seegsmsconfig = API.service('seegsmsconfigs-show', API.all('seegsmsconfigs'))
        let detail = 0;
        Seegsmsconfig.one(detail).get()
            .then((response) => {
                this.seegsmsconfigs = API.copy(response);
                $log.log('Seegsmsconfigs:',this.seegsmsconfigs);
            }, function (respdata) {
            $log.log('Error report', respdata);
            let alert = { type: 'error', 'title': 'Erreur !', msg: respdata.statusText };
            $state.go($state.current, { alerts: alert})
        });
    }

    $onInit(){
    }

    save (isValid) {
        if (isValid) {
            let $state = this.$state;
            let $log = this.$log;

            //this.seegsmsconfigs.route = 'configs';

            $log.log('Seegsmsconfig bfor update',this.seegsmsconfigs);

            this.seegsmsconfigs.put()
                .then((response) => {
                    $log.log('response success',response);
                    let alert = { type: 'success', 'title': 'Succès!', msg: 'Configuration modifiée.' }
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
}

export const SeegsmsconfigEditComponent = {
    templateUrl: './views/app/components/seegsmsconfig-edit/seegsmsconfig-edit.component.html',
    controller: SeegsmsconfigEditController,
    controllerAs: 'vm',
    bindings: {}
}


