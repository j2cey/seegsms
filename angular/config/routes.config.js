export function RoutesConfig ($stateProvider, $urlRouterProvider) {
  'ngInject'

  var getView = (viewName) => {
    return `./views/app/pages/${viewName}/${viewName}.page.html`
  }

  var getLayout = (layout) => {
    return `./views/app/pages/layout/${layout}.page.html`
  }

  $urlRouterProvider.otherwise('/')

  $stateProvider
    .state('app', {
      abstract: true,
      views: {
        'layout': {
          templateUrl: getLayout('layout')
        },
        'header@app': {
          templateUrl: getView('header')
        },
        'footer@app': {
          templateUrl: getView('footer')
        },
        main: {}
      },
      data: {
        bodyClass: 'hold-transition skin-blue sidebar-mini'
      }
    })
    .state('app.landing', {
      url: '/',
      data: {
        auth: true
      },
      views: {
        'main@app': {
          templateUrl: getView('landing')
        }
      }
    })
    .state('app.comingsoon', {
      url: '/comingsoon',
      data: {
        auth: true
      },
      views: {
        'main@app': {
          template: '<comingSoon></comingSoon>'
        }
      }
    })
    .state('app.profile', {
      url: '/profile',
      data: {
        auth: true
      },
      views: {
        'main@app': {
          template: '<userProfile></userProfile>'
        }
      },
      params: {
        alerts: null
      }
    })
    .state('app.userlist', {
      url: '/user-lists',
      data: {
        auth: true
      },
      views: {
        'main@app': {
          template: '<userLists></userLists>'
        }
      }
    })
    .state('app.sestest', {
      url: '/ses-test',
      data: {
        auth: true
      },
      views: {
        'main@app': {
          template: '<sesTest></sesTest>'
        }
      }
    })
    .state('app.sestest2', {
      url: '/ses-test2',
      data: {
        auth: true
      },
      views: {
        'main@app': {
          template: '<sesTest2></sesTest2>'
        }
      }
    })
    .state('app.useredit', {
      url: '/user-edit/:userId',
      data: {
        auth: true
      },
      views: {
        'main@app': {
          template: '<userEdit></userEdit>'
        }
      },
      params: {
        alerts: null,
        userId: null
      }
    })
    .state('app.userroles', {
      url: '/user-roles',
      data: {
        auth: true
      },
      views: {
        'main@app': {
          template: '<userRoles></userRoles>'
        }
      }
    })
    .state('app.userpermissions', {
      url: '/user-permissions',
      data: {
        auth: true
      },
      views: {
        'main@app': {
          template: '<userPermissions></userPermissions>'
        }
      }
    })
    .state('app.userpermissionsadd', {
      url: '/user-permissions-add',
      data: {
        auth: true
      },
      views: {
        'main@app': {
          template: '<userPermissionsAdd></userPermissionsAdd>'
        }
      },
      params: {
        alerts: null
      }
    })
    .state('app.userpermissionsedit', {
      url: '/user-permissions-edit/:permissionId',
      data: {
        auth: true
      },
      views: {
        'main@app': {
          template: '<userPermissionsEdit></userPermissionsEdit>'
        }
      },
      params: {
        alerts: null,
        permissionId: null
      }
    })
    .state('app.userrolesadd', {
      url: '/user-roles-add',
      data: {
        auth: true
      },
      views: {
        'main@app': {
          template: '<userRolesAdd></userRolesAdd>'
        }
      },
      params: {
        alerts: null
      }
    })
    .state('app.userrolesedit', {
      url: '/user-roles-edit/:roleId',
      data: {
        auth: true
      },
      views: {
        'main@app': {
          template: '<userRolesEdit></userRolesEdit>'
        }
      },
      params: {
        alerts: null,
        roleId: null
      }
    })
    .state('app.widgets', {
      url: '/widgets',
      data: {
        auth: true
      },
      views: {
        'main@app': {
          template: '<widgets></widgets>'
        }
      }
    })
    .state('login', {
      url: '/login',
      views: {
        'layout': {
          templateUrl: getView('login')
        },
        'header@app': {},
        'footer@app': {}
      },
      data: {
        bodyClass: 'hold-transition login-page'
      },
      params: {
        registerSuccess: null,
        successMsg: null
      }
    })
    .state('loginloader', {
      url: '/login-loader',
      views: {
        'layout': {
          templateUrl: getView('login-loader')
        },
        'header@app': {},
        'footer@app': {}
      },
      data: {
        bodyClass: 'hold-transition login-page'
      }
    })
    .state('register', {
      url: '/register',
      views: {
        'layout': {
          templateUrl: getView('register')
        },
        'header@app': {},
        'footer@app': {}
      },
      data: {
        bodyClass: 'hold-transition register-page'
      }
    })
    .state('userverification', {
      url: '/userverification/:status',
      views: {
        'layout': {
          templateUrl: getView('user-verification')
        }
      },
      data: {
        bodyClass: 'hold-transition login-page'
      },
      params: {
        status: null
      }
    })
    .state('forgot_password', {
      url: '/forgot-password',
      views: {
        'layout': {
          templateUrl: getView('forgot-password')
        },
        'header@app': {},
        'footer@app': {}
      },
      data: {
        bodyClass: 'hold-transition login-page'
      }
    })
    .state('reset_password', {
      url: '/reset-password/:email/:token',
      views: {
        'layout': {
          templateUrl: getView('reset-password')
        },
        'header@app': {},
        'footer@app': {}
      },
      data: {
        bodyClass: 'hold-transition login-page'
      }
    })
    .state('app.logout', {
      url: '/logout',
      views: {
        'main@app': {
          controller: function ($rootScope, $scope, $auth, $state, AclService) {
            $auth.logout().then(function () {
              delete $rootScope.me
              AclService.flushRoles()
              AclService.setAbilities({})
              $state.go('login')
            })
          }
        }
      }
    })

      /*Campaign routes*/

    .state('app.campaignadd', {
      url: '/campaign-add',
      data: {
        auth: true
      },
      views: {
        'main@app': {
          template: '<campaignAdd></campaignAdd>'
        }
      },
      params: {
        alerts: null
      }
    })
    .state('app.campaignsparams', {
      url: '/campaigns-params',
      data: {
        auth: true
      },
      views: {
        'main@app': {
          template: '<campaignsParams></campaignsParams>'
        }
      },
      params: {
        alerts: null
      }
    })
    .state('app.campaignslist', {
      url: '/campaigns-list',
      data: {
        auth: true
      },
      views: {
        'main@app': {
          template: '<campaignsList></campaignsList>'
        }
      },
      params: {
        limit: null,
        alerts: null
      }
    })
    .state('app.campaignsedit', {
      url: '/campaigns-edit',
      data: {
        auth: true
      },
      views: {
        'main@app': {
          template: '<campaignsEdit></campaignsEdit>'
        }
      },
      params: {
        alerts: null,
        campaignId: null
      }
    })
    .state('app.campaignsvalidate', {
      url: '/campaigns-validate',
      data: {
        auth: true
      },
      views: {
        'main@app': {
          template: '<campaignsValidate></campaignsValidate>'
        }
      },
      params: {
        alerts: null,
        userId: null
      }
    })
    .state('app.campaignssending', {
      url: '/campaigns-sending',
      data: {
        auth: true
      },
      views: {
        'main@app': {
          template: '<campaignsSending></campaignsSending>'
        }
      },
      params: {
        alerts: null
      }
    })

    /*Trace routes*/

    .state('app.tracelists', {
      url: '/trace-lists',
      data: {
        auth: true
      },
      views: {
        'main@app': {
          template: '<traceLists></traceLists>'
        }
      },
      params: {
        alerts: null
      }
    })
    .state('app.traceedit', {
      url: '/trace-edit',
      data: {
        auth: true
      },
      views: {
        'main@app': {
          template: '<traceEdit></traceEdit>'
        }
      },
      params: {
        alerts: null,
        traceId: null
      }
    })

  /* SEEGSMS Config routes */

      .state('app.seegsmsconfigedit', {
        url: '/seegsmsconfigs-edit',
        data: {
          auth: true
        },
        views: {
          'main@app': {
            template: '<seegsmsconfigEdit></seegsmsconfigEdit>'
          }
        },
        params: {
          alerts: null,
          detail: null
        }
      })

      /*Campaignplannings routes*/

      .state('app.campaignplanningsadd', {
        url: '/campaignplannings-add',
        data: {
          auth: true
        },
        views: {
          'main@app': {
            template: '<campaignplanningsAdd></campaignplanningsAdd>'
          }
        },
        params: {
          alerts: null
        }
      })
      .state('app.campaignplanningsedit', {
        url: '/campaignplannings-edit',
        data: {
          auth: true
        },
        views: {
          'main@app': {
            template: '<campaignplanningsEdit></campaignplanningsEdit>'
          }
        },
        params: {
          alerts: null,
          planningId: null
        }
      })

  /* Dashboard routes */

}
