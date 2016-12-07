class CampaignsValidateController{
    constructor($stateParams, $state, API, $log){
        'ngInject';

        this.$log = $log;
        this.$state = $state;
        this.API = API;
        this.alerts = [];

        this.validatings = [];
        this.validatingsDone = [];

        if ($stateParams.alerts) {
            this.alerts.push($stateParams.alerts)
        }

        let userId = $stateParams.userId
        this.userId = userId;

        $log.log('userId',userId);

        let Planningvalidating = API.service('planningvalidating-show', API.all('campaigns'));
        Planningvalidating.one(userId).get()
            .then((response) => {
                $log.log('validating resp',response);
                $log.log('validating data',response.data);
                this.validatings = response.data.planningvalidating;
            });
    }

    validate(validatingcurr,action,frmvalidatings){

        let Planningvalidating = this.API.service('planningvalidatings', this.API.all('campaigns'));
        let $state = this.$state;
        let $log = this.$log;

        $log.log('frm validatings',frmvalidatings);
        $log.log('validating curr',validatingcurr);

        let $currvalidatingindex = frmvalidatings.indexOf(validatingcurr);

        $log.log('frm validatings curr index',$currvalidatingindex);

        Planningvalidating.post({
            'user_id': this.userId,
            'model': "Campaignplannings",
            'validatingdata': angular.toJson([{model_id: validatingcurr.planning_id, action: action}])
        }).then(function (response) {

            $log.log('validating response',response);

            frmvalidatings.splice($currvalidatingindex, 1);

            $log.log('validatings after row drop 1 ',frmvalidatings);

            let alert = { type: 'success', 'title': 'Succès!', msg: 'Permission ajoutée.' }
            $state.go($state.current, { alerts: alert})
        }, function (response) {
            let alert = { type: 'error', 'title': 'Error!', msg: response.data.message }
            $state.go($state.current, { alerts: alert})
        })
    }

    $onInit(){
    }
}

export const CampaignsValidateComponent = {
    templateUrl: './views/app/components/campaigns-validate/campaigns-validate.component.html',
    controller: CampaignsValidateController,
    controllerAs: 'vm',
    bindings: {}
}


