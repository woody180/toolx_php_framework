const baseurl = document.querySelector('meta[name="baseurl"]').getAttribute('content');

import FileManagerController from "../js/classes/admin/FileManagerController.js";


new FileManagerController({
    baseurl: baseurl,
    editorClass: '.tiny-text-area',
    onInsert: (data) => {
        console.log(data.files[0]);
        const input = document.querySelector('.tox-dialog__body input');
        if (input) input.value = data.files[0];
    }
});
