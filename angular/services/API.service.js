export class APIService {
  constructor (Restangular, $window, $log) {
    'ngInject'
    // content negotiation
    var headers = {
      'Content-Type': 'application/json',
      'Accept': 'application/x.laravel.v1+json'
    }

    return Restangular.withConfig(function (RestangularConfigurer) {
      RestangularConfigurer
        .setBaseUrl('/api/')
        .setDefaultHeaders(headers)
        .setErrorInterceptor(function (response) {
          if (response.status === 422) {
            // for (var error in response.data.errors) {
            // return ToastService.error(response.data.errors[error][0])
            // }
          }
        })
        .addFullRequestInterceptor(function (element, operation, what, url, headers) {
          var token = $window.localStorage.satellizer_token
          if (token) {
            headers.Authorization = 'Bearer ' + token
          }
        })
        .addResponseInterceptor(function (response, operation, what) {
          if (operation === 'getList') {

            $log.log('API operation',operation);

            $log.log('API what',what);

            $log.log('API response',response);

            var newResponse = response.data[what]

            $log.log('API newresponse',newResponse);

            newResponse.error = response.error
            return newResponse
          }

          return response
        })
    })
  }
}
