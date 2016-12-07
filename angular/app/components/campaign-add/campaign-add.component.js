class CampaignAddController{
    constructor(API, $state, $stateParams, $log, $http, MultipartFormService, $filter, ContextService){
        'ngInject'

        this.$filter = $filter;
        this.MultipartFormService = MultipartFormService;
        this.$http = $http;
        //this.campaigntypeselected = -1;
        this.$log = $log;
        this.$state = $state;
        this.formSubmitted = false;
        this.API = API;
        this.alerts = [];

        if ($stateParams.alerts) {
            this.alerts.push($stateParams.alerts)
        }

        this.step = 1;
        this.plantypes = ['Maintenant', 'Ultérieurement'];

        this.mindate = new Date();

        let CampaigntypesApi = API.service('campaigntypes', this.API.all('campaigns'))
        CampaigntypesApi.getList()
            .then((response) => {
                let campaigntypes = []
                let campaigntypesResponse = response.plain()

                angular.forEach(campaigntypesResponse, function (value) {
                    campaigntypes.push({id: value.id, title: value.title})
                })

                this.campaigntypes = campaigntypes
            })

        let navHeader = this

        ContextService.me(function (data) {
            navHeader.userData = data
        })
    }

    submit (){

        let $filter = this.$filter;
        let $log = this.$log;
        let $state = this.$state;
        var urlApi = '/api/campaigns/campaigns';

        //var files = [];//'receiversfile'=>
        /*files.push( {} );*/
        var files = {'receiversfile':this.campaign.receiversfile};

        // formate date
        this.campaign.data.plandate = $filter('date')(this.campaign.data.plandate, "yyyy-MM-dd HH:mm:ss");//new Date(this.campaign.data.plandate); //moment(this.campaign.data.plandate).format("yyyy-MM-dd HH:mm:ss");//

        // User data
        this.campaign.data.userData = angular.toJson(this.userData);
        // Campaign type
        this.campaign.data.type = angular.toJson(this.campaign.type);
        // Campaign file
        if (angular.isDefined(this.campaign.campaignfile)) {
            if (this.campaign.campaignfile == true) {
                this.campaign.data.campaignfile = this.campaign.campaignfile;
            }else{
                // no campaign file
            }
        }else{
            // no campaign file
        }

        $log.log(this.campaign.receiversfile);
        $log.log(this.campaign.data);

        var response = this.MultipartFormService.uploadForm(urlApi,this.campaign.data,files);

        response.then(function (respdata) {
            $log.log(respdata);
            let alert = { type: 'success', 'title': 'Succès !', msg: 'Campagne créée.' };
            $state.go($state.current, { alerts: alert})
        }, function (respdata) {
            $log.log(respdata);
            let alert = { type: 'error', 'title': 'Erreur !', msg: respdata.statusText };
            $state.go($state.current, { alerts: alert})
        });
    }

    nextStep () {
        if (this.step <= 3){
            // Next
            this.step++;

        }else{
            // Submit
        }
    }

    prevStep () {
        this.step--;
    }

    showStep ($steptoshow) {
        this.step = $steptoshow;
    }

    isSet ($steptocompare) {
        return (this.step === $steptocompare);
    }

    setPlanDate (){
        if (this.campaign.plantype === 'Maintenant'){
            this.campaign.data.plandate = new Date();//this.$filter('date')(new Date());'dd-MM-yyyy HH:mm:ss'
        } else {
            this.campaign.data.plandate = "";
        }
    }

    setCampaignType () {
        this.campaign.data.type = this.campaign.type.id;
    }

    checkFormStepErrors ($form,campaignfile) {
        //return this.checkStepErrors(form,this.step);

        var stepformerror = false;

        if (this.step === 1){
            stepformerror = ( ($form.type.$invalid) || ($form.title.$invalid) );
        } else if (this.step === 2){
            stepformerror = ( ($form.msg.$invalid && !campaignfile) || ($form.plandate.$invalid) );
        } else if (this.step === 3){
            //stepformerror = ( $form.desti.$invalid );
            //stepformerror = ( $form.$error.required );
            stepformerror = (!(this.campaign.receiversfile));
        } else {
            stepformerror = false;
        }

        return stepformerror;
    }




    save (isValid) {
        this.$state.go(this.$state.current, {}, { alerts: 'test' })
        if (isValid) {
            //let Campaigns = this.API.service('campaigns')
            let Campaigns = this.API.service('campaigns', this.API.all('campaigns'))
            let $state = this.$state

            //var fd = new FormData();

            /*fd.append("title",this.campaign.title);
            fd.append("descript", this.campaign.descript);
            fd.append("msg", this.campaign.msg);
            fd.append("campaigntype_id", this.campaign.type.id);
            fd.append("receiversfile", this.campaign.desti);*/

            /*var fd = new FormData();
            for(var key in this.campaign)
                fd.append(key, this.campaign[key]);*/

            /*var fd = {
                'title': this.campaign.title,
                'descript': this.campaign.descript,
                'msg': this.campaign.msg,
                'campaigntype_id': this.campaign.type.id,
                'receiversfile': this.campaign.myfile
            }*/

            //console.log(fd);

            Campaigns.post(
                /*
                fd, {
                transformRequest: angular.indentity,
                headers: {
                    //'Content-Type': undefined
                    'Content-Type': 'multipart/form-data'
                }
            }*/

                {
                    method: 'POST',
                    url: '/api/campaigns/campaigns',
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    },
                    data: {
                        title: this.campaign.title,
                        descript: this.campaign.descript,
                        msg: this.campaign.msg,
                        campaigntype_id: this.campaign.type.id,
                        receiversfile: this.campaign.myfile
                    },
                    transformRequest: function (data, headersGetter) {
                        var formData = new FormData();
                        angular.forEach(data, function (value, key) {
                            formData.append(key, value);
                        });

                        var headers = headersGetter();
                        delete headers['Content-Type'];

                        return formData;
                    }
                }

            ).then(function () {
                //console.log(respdata);
                let alert = { type: 'success', 'title': 'Succès !', msg: 'Campagne créée et planifiée.' }
                $state.go($state.current, { alerts: alert})
            }, function (response) {
                let alert = { type: 'error', 'title': 'Erreur !', msg: response }
                $state.go($state.current, { alerts: alert})
            })
        } else {
            this.formSubmitted = true
        }
    }

    assignFile (files) {
        this.campaign.file = files[0];
        //console.log(this.campaign);
    }

    uploadFile (files) {
        /*let $state = this.$state*/
        var uploadUrl = '/api/campaigns/campaigns';
        /*var fd = new FormData();
        //Take the first selected file
        fd.append("file", files[0])*/

        var fd = {
            'title': this.campaign.title,
            'descript': this.campaign.descript,
            'msg': this.campaign.msg,
            'campaigntype_id': this.campaign.type.id,
            'receiversfile': files[0]
        }

        //console.log(files);

        this.$http(uploadUrl, {}, {
            postWithFile: {
                method: "POST",
                params: fd,
                transformRequest: angular.identity,
                headers: { 'Content-Type': undefined }
            }
        }).postWithFile(fd).then(function(){
            //successful
            //console.log(response);
        },function(){
            //error
            //console.log(error);
        });

        /*this.$http.post(uploadUrl, fd, {
            withCredentials: true,
            headers: {'Content-Type': undefined },
            transformRequest: angular.identity
        }).success(
            function (respdata) {
                console.log(respdata);
                let alert = { type: 'success', 'title': 'Succès !', msg: 'Campagne créée et planifiée.' }
                $state.go($state.current, { alerts: alert})
            }
        ).error(
            function (response) {
                let alert = { type: 'error', 'title': 'Erreur !', msg: response }
                $state.go($state.current, { alerts: alert})
            }
        );*/

    }

    // upload on file select or drop
    upload (file) {
        this.Upload.upload({
            url: '/api/campaigns/campaigns',
            data: {
                title: this.campaign.title,
                descript: this.campaign.descript,
                msg: this.campaign.msg,
                campaigntype_id: this.campaign.type.id,
                receiversfile: file
            }
        }).then(function () {
            //console.log('Success ' + resp.config.data.file.name + 'uploaded. Response: ' + resp.data);
        }, function () {
            //console.log('Error status: ' + resp.status);
        }, function () {
            /*var progressPercentage = parseInt(100.0 * evt.loaded / evt.total);*/
            //console.log('progress: ' + progressPercentage + '% ' + evt.config.data.file.name);
        });
    }

    /*submit (){
        var uploadUrl = '/api/campaigns/campaigns';
        this.MultipartFormService.post(uploadUrl,this.campaign)
    }*/

    /*checkStepErrors (form, step) {
        var steperror = false;

        if (step === 1){
            steperror = ( (form.type.$invalid) || (form.title.$invalid) );
        } else if (step === 2){
            steperror = ( (form.msg.$invalid) || (form.plandate.$invalid) );
        } else if (step === 3){
            steperror = ( form.desti.$invalid );
        } else {
            steperror = false;
        }

        return steperror;
    }*/

    $onInit(){
    }
}

export const CampaignAddComponent = {
    templateUrl: './views/app/components/campaign-add/campaign-add.component.html',
    controller: CampaignAddController,
    controllerAs: 'vm',
    bindings: {}
}


