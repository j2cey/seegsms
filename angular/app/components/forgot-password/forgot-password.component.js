class ForgotPasswordController {
  constructor (API, $state) {
    'ngInject'

    this.API = API
    this.$state = $state
    this.errorTrigger = false
  }

  $onInit () {
    this.email = ''
  }

  submit () {
    this.API.all('auth/password/email').post({
      email: this.email
    }).then(() => {
      this.$state.go('login', { successMsg: `Vérifiez votre boîte email pour lire les instructions sur la réinitialisation de votre mot de passe.` })
    }, () => {
      this.errorTrigger = true
    })
  }
}

export const ForgotPasswordComponent = {
  templateUrl: './views/app/components/forgot-password/forgot-password.component.html',
  controller: ForgotPasswordController,
  controllerAs: 'vm',
  bindings: {}
}
