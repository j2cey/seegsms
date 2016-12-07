import { RouteBodyClassComponent } from './directives/route-bodyclass/route-bodyclass.component'
import { PasswordVerifyClassComponent } from './directives/password-verify/password-verify.component'
import { FileModelClassComponent } from './directives/file-model/file-model'
import { ValidFileClassComponent } from './directives/valid-file/valid-file'

angular.module('app.components')
  .directive('routeBodyclass', RouteBodyClassComponent)
  .directive('passwordVerify', PasswordVerifyClassComponent)
  .directive('fileModel', FileModelClassComponent)
  .directive('validFile', ValidFileClassComponent)
