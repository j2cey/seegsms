import {LittlenotificationsService} from './services/littlenotifications.service';
import {FileUploadService} from './services/fileUpload.service'
import { MultipartFormService } from './services/multipartForm.service'
import { ContextService } from './services/context.service'
import { APIService } from './services/API.service'
import { DialogService } from './services/dialog.service'
import { ToastService } from './services/toast.service'

angular.module('app.services')
	.service('LittlenotificationsService', LittlenotificationsService)
	.service('FileUploadService', FileUploadService)
	.service('MultipartFormService', MultipartFormService)
  .service('ContextService', ContextService)
  .service('API', APIService)
  .service('DialogService', DialogService)
  .service('ToastService', ToastService)
