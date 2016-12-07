class UserVerificationController {
  constructor ($stateParams) {
    'ngInject'
    this.alerts = []

    if ($stateParams.status === 'success') {
      this.alerts.push({ type: 'success', 'title': 'Succes!', msg: 'Succes Verification Email.' })
    } else {
      this.alerts.push({ type: 'danger', 'title': 'Erreur:', msg: 'Echec verification Email.' })
    }
  }

  $onInit () {}
}

export const UserVerificationComponent = {
  templateUrl: './views/app/components/user-verification/user-verification.component.html',
  controller: UserVerificationController,
  controllerAs: 'vm',
  bindings: {}
}
