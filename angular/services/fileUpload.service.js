export class FileUploadService{
    constructor($http){
        'ngInject';

        this.$http = $http;
    }

    uploadFileToUrl (uploadUrl) {//, data) {
        //var data = {}; //file object

        /*var fd = new FormData();
        fd.append('file', data.file);*/

        // "endpoint server path to whom sending file"
        /*this.$http.post(uploadUrl, fd, {
                withCredentials: false,
                headers: {
                    'Content-Type': undefined
                },
                transformRequest: angular.identity,
                params: {
                    fd
                },
                responseType: "arraybuffer"
            })
            .success(function(response, status, headers, config) {
                //console.log(response);

                if (status == 200 || status == 202){
                    //do whatever in success
                } else {
                    // handle error in  else if needed
                }
            })
            .error(function(error, status, headers, config) {
                //console.log(error);

                // handle else calls
            });*/
    }
}

