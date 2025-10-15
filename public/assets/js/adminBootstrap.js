const baseurl = document.querySelector('meta[name="baseurl"]').getAttribute('content');

import FileManagerController from "../js/classes/admin/FileManagerController.js";


new FileManagerController(baseurl);