import {CampaignplanningsAddComponent} from './app/components/campaignplannings-add/campaignplannings-add.component'
import {CampaignplanningsEditComponent} from './app/components/campaignplannings-edit/campaignplannings-edit.component'
import {SeegsmsconfigEditComponent} from './app/components/seegsmsconfig-edit/seegsmsconfig-edit.component'
import {CampaignsSendingComponent} from './app/components/campaigns-sending/campaigns-sending.component'
import {CampaignsEditComponent} from './app/components/campaigns-edit/campaigns-edit.component'
import {CampaignsValidateComponent} from './app/components/campaigns-validate/campaigns-validate.component'
import {TraceEditComponent} from './app/components/trace-edit/trace-edit.component'
import {TraceListsComponent} from './app/components/trace-lists/trace-lists.component'
import {CampaignsListComponent} from './app/components/campaigns-list/campaigns-list.component'
import {CampaignsParamsComponent} from './app/components/campaigns-params/campaigns-params.component'
import {CampaignAddComponent} from './app/components/campaign-add/campaign-add.component'
import {SesTest2Component} from './app/components/ses-test2/ses-test2.component'
import {SesTestComponent} from './app/components/ses-test/ses-test.component'
import { WidgetsComponent } from './app/components/widgets/widgets.component'
import { UserProfileComponent } from './app/components/user-profile/user-profile.component'
import { UserVerificationComponent } from './app/components/user-verification/user-verification.component'
import { ComingSoonComponent } from './app/components/coming-soon/coming-soon.component'
import { UserEditComponent } from './app/components/user-edit/user-edit.component'
import { UserPermissionsEditComponent } from './app/components/user-permissions-edit/user-permissions-edit.component'
import { UserPermissionsAddComponent } from './app/components/user-permissions-add/user-permissions-add.component'
import { UserPermissionsComponent } from './app/components/user-permissions/user-permissions.component'
import { UserRolesEditComponent } from './app/components/user-roles-edit/user-roles-edit.component'
import { UserRolesAddComponent } from './app/components/user-roles-add/user-roles-add.component'
import { UserRolesComponent } from './app/components/user-roles/user-roles.component'
import { UserListsComponent } from './app/components/user-lists/user-lists.component'
import { DashboardComponent } from './app/components/dashboard/dashboard.component'
import { NavSidebarComponent } from './app/components/nav-sidebar/nav-sidebar.component'
import { NavHeaderComponent } from './app/components/nav-header/nav-header.component'
import { LoginLoaderComponent } from './app/components/login-loader/login-loader.component'
import { ResetPasswordComponent } from './app/components/reset-password/reset-password.component'
import { ForgotPasswordComponent } from './app/components/forgot-password/forgot-password.component'
import { LoginFormComponent } from './app/components/login-form/login-form.component'
import { RegisterFormComponent } from './app/components/register-form/register-form.component'

angular.module('app.components')
	.component('campaignplanningsadd', CampaignplanningsAddComponent)
	.component('campaignplanningsedit', CampaignplanningsEditComponent)
	.component('seegsmsconfigedit', SeegsmsconfigEditComponent)
	.component('campaignssending', CampaignsSendingComponent)
	.component('campaignsedit', CampaignsEditComponent)
	.component('campaignsvalidate', CampaignsValidateComponent)
	.component('traceedit', TraceEditComponent)
	.component('tracelists', TraceListsComponent)
	.component('campaignslist', CampaignsListComponent)
	.component('campaignsparams', CampaignsParamsComponent)
	.component('campaignadd', CampaignAddComponent)
	.component('sestest2', SesTest2Component)
	.component('sestest', SesTestComponent)
	.component('widgets', WidgetsComponent)
	.component('userprofile', UserProfileComponent)
	.component('userVerification', UserVerificationComponent)
	.component('comingsoon', ComingSoonComponent)
	.component('useredit', UserEditComponent)
	.component('userpermissionsedit', UserPermissionsEditComponent)
	.component('userpermissionsadd', UserPermissionsAddComponent)
	.component('userpermissions', UserPermissionsComponent)
	.component('userrolesedit', UserRolesEditComponent)
	.component('userrolesadd', UserRolesAddComponent)
	.component('userroles', UserRolesComponent)
	.component('userlists', UserListsComponent)
	.component('dashboard', DashboardComponent)
	.component('navSidebar', NavSidebarComponent)
	.component('navHeader', NavHeaderComponent)
	.component('loginLoader', LoginLoaderComponent)
	.component('resetPassword', ResetPasswordComponent)
	.component('forgotPassword', ForgotPasswordComponent)
	.component('loginForm', LoginFormComponent)
	.component('registerForm', RegisterFormComponent)
