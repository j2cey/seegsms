export class MultipartFormService{
    constructor($http){
        'ngInject';

        this.$http = $http;
    }

    /*post (uploadUrl, data) {
        var fd = new FormData();
        for(var key in data)
            fd.append(key, data[key]);
        this.$http.post(uploadUrl, fd, {
            transformRequest: angular.identity,
            headers: { 'Content-Type': undefined }
        })
    }*/


    /*uploadBeat (data,image,tagged_file,untagged_file){
        var fd = new FormData();
        fd.append('image', image);
        fd.append('tagged_file', tagged_file);
        fd.append('untagged_file', untagged_file);

        angular.forEach(data, function(value, key) {
            fd.append(key,value);
        });
        console.log(fd); // fd is null , I don't know why?
        var req = {
            method: 'POST',
            transformRequest: angular.identity,
            url: '/api/campaigns/campaigns',
            data: fd,
            headers:{
                'Content-Type': undefined,
            }
        }
        return this.$http(req);
    }*/

    uploadForm (urlApi, data, files){
        /*new form data*/
        var fd = new FormData();

        /*files looping*/
        angular.forEach(files, function(filevalue, filekey) {
            fd.append(filekey,filevalue);
        });
        /*fd.append('image', image);
        fd.append('tagged_file', tagged_file);
        fd.append('untagged_file', untagged_file);*/

        angular.forEach(data, function(value, key) {
            fd.append(key,value);
        });
        /*console.log(fd);*/
        var req = {
            method: 'POST',
            transformRequest: angular.identity,
            url: urlApi,//'/api/campaigns/campaigns',
            data: fd,
            headers:{
                'Content-Type': undefined
            }
        }
        return this.$http(req);
    }
}