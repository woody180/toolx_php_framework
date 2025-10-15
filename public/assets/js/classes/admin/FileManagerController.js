import SketchEngine from '../../SketchEngine.js';

export default class FileManagerController extends SketchEngine {
    constructor(baseurl) {
        super();
        this.variables.baseurl = baseurl;


        fetch(`${baseurl}/filemanager/get-server-info`, {
            method: 'GET',
            headers: {
                "X-Requested-With": "XMLHttpRequest"
            }
        })
        .then(response => response.json())
        .then(data => {
            
            if (data.maxFileUploads) {
                this.variables.maxFileUpload = data.maxFileUploads
            }
        })
        .catch(error => {
            console.error('Error fetching server info:', error);
        });
    }


    variables = {
        baseurl: undefined,
        filemanagerDirectory: undefined, // url
        
        selectedIndexes: [],
        maxFileUpload: 9,
        compressionAction: 'zip', // zip or unzip
        isFullscreen: localStorage.getItem('toolx_fl_fullscreen') === 'true' ? true : false,
        selectedType: undefined, // Can be mixed, file, folder. Mixed means both files and folders or multile files
        searchInput: '#fl-manager-items-search',
        closeSvg: '<svg width="25" height="25" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="Menu / Close_SM"><path id="Vector" d="M16 16L12 12M12 12L8 8M12 12L16 8M12 12L8 16" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></g></svg>',
        searchCloseOriginalIcon: '<svg width="20" height="20" viewBox="0 0 20 20"><circle fill="none" stroke="#000" stroke-width="1.1" cx="9" cy="9" r="7"></circle><path fill="none" stroke="#000" stroke-width="1.1" d="M14,14 L18,18 L14,14 Z"></path></svg>',
        searchAll: false
    };


    execute = [
        'tinymceInit',
    ];


    selectors = {
        tinyArea: '.tiny-text-area',
        compressSelecetedButton: '#fl-compress-selected',
        flModal: '#filemanager-modal',
        renameItemButton: '#fl-rename-item',
        fileManagerInfo: '#fl-file-information',
        fileManagerContainer: '#file-information-container',
        closeInfoBarButton: '#fl-close-info-bar',
        downloadSelectedButton: '#fl-download-selected',
        fullscreenButton: '#fl-fullscreen-button',
        searchIcon: '#fl-clear-search-icon',
        searchAllCheckbox: '#fl-search-all',
        clearSearchButton: '#fl-clear-search-icon',
        gridContainer: '#fl-modal-data #filemanager'
    };


    html = {}


    catchDOM() {}


    bindEvents() {
        // File manager events
        this.lib('body').on('click', e => {
            e.preventDefault();
        }, '#filemanager .item-dir');


        // Search all checkbox
        this.lib('body').on('change', e => {
            e.target.checked ? this.variables.searchAll = true : this.variables.searchAll = false;
            console.log(this.variables.searchAll);
            
        }, this.selectors.searchAllCheckbox);


        // Clear search results
        this.lib('body').on('click', e => {
            e.preventDefault();
            this.functions.clearSearch.call(this);
        }, this.selectors.clearSearchButton);


        // Search
        this.lib('body').on('keydown', e => {
            if (e.key === 'Enter' || e.keyCode === 13) {
                e.preventDefault();
                this.functions.search.call(this, e);
            }
        }, this.selectors.searchInput);


        this.lib('body').on('click', this.functions.triggerDownload.bind(this), this.selectors.downloadSelectedButton);


        // Close file information bar
        this.lib('body').on('click', e => {
            e.preventDefault();
            document.querySelector(this.selectors.fileManagerInfo).innerHTML = '';
            document.querySelector(this.selectors.fileManagerContainer).classList.remove('active');

            // remove all selected items
            this.variables.selectedIndexes = [];
            document.querySelectorAll('.fl-item-select').forEach(item => item.checked = false);
            // let removeBtn = document.querySelector('#fl-remove-selected');
            // let insertButton = document.querySelector('#fl-insert-button');
            // let compressButton = document.querySelector(this.selectors.compressSelecetedButton);
            // let renameButton = document.querySelector(this.selectors.renameItemButton);

            // remove active classes
            document.querySelectorAll('#filemanager-toolbar button.active').forEach(btn => {
                if (btn.id != 'fl-fullscreen-button') btn.classList.remove('active', 'fl-background-success')
            });

        }, this.selectors.closeInfoBarButton);


        // Toggle fullscreen
        this.lib('body').on('click', this.functions.toggleFullscreen.bind(this), this.selectors.fullscreenButton);
        

        // Open folder on double click
        this.lib('body').on('dblclick', e => {
            e.preventDefault();
            
            const url = e.target.closest('div').getAttribute('data-href');

            this.variables.filemanagerDirectory = url;
            
            this.functions.renderFileManager.call(this, url, function(html) {
                const modalElement = document.querySelector('#filemanager').closest('.uk-modal').querySelector('.uk-modal-body');
                modalElement.innerHTML = html;  
            });

            e.stopPropagation();
        }, '#filemanager .fl-item-dir');


        // Back button
        this.lib('body').on('click', e => {
            e.preventDefault();
            const url = e.target.closest('a').href;

            this.variables.filemanagerDirectory = url
            
            this.functions.renderFileManager.call(this, url, function(html) {
                const modalElement = document.querySelector('#filemanager').closest('.uk-modal').querySelector('.uk-modal-body');
                modalElement.innerHTML = html;
            });

            e.stopPropagation();
        }, '.fl-back-dir-button');


        // Upload files / images
        this.lib('body').on('change', e => {
            
            const files = e.target.files;
            const csrfToken = document.querySelector('[name="csrf_token"]').value;

            console.log(this.variables.maxFileUpload);
            
            
            // Error message for maximum file upload
            if (files.length > this.variables.maxFileUpload) return alert('Maximum file upload at a time is ' + this.variables.maxFileUpload);

            // console.log(this.variables.filemanagerDirectory);
            
            if (files && files.length > 0) {
                const formData = new FormData();
                formData.append('csrf_token', csrfToken);
                formData.append('uploadDir', document.querySelector('[name="filemanager_path"]').value);
                Array.from(files).forEach(file => {
                    formData.append('images[]', file);
                });

                document.querySelector('#loading-animation').classList.remove('uk-hidden');
                document.querySelector('#fl-loading-progress').innerText = 'Uploading and caching files. Please wait...';

                const xhr = new XMLHttpRequest();
                xhr.open('POST', `${this.variables.baseurl}/filemanager/upload`, true);
                xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");

                xhr.upload.onprogress = (event) => {
                    if (event.lengthComputable) {
                        const percentComplete = Math.round((event.loaded / event.total) * 100);
                        document.querySelector('#fl-loading-progress').innerText = `Uploading and caching files... ${percentComplete}%. Please wait...`;
                    }
                };

                xhr.onload = () => {
                    try {
                        const data = JSON.parse(xhr.responseText);
                        if (data.status && data.status == 'error') {
                            return alert(data.message.images.join('\n'));
                        }
                        UIkit.notification(`<p class="uk-margin-remove uk-text-small">${data.success}</p>`, {pos: 'bottom-center'});

                        this.functions.renderFileManager.call(this, this.variables.filemanagerDirectory, function(html) {
                            const modalElement = document.querySelector('#filemanager').closest('.uk-modal').querySelector('.uk-modal-body');
                            modalElement.innerHTML = html;  
                        });

                        this.variables.selectedIndexes = [];
                    } catch (error) {
                        alert('Upload failed: ' + error);
                    }
                };

                xhr.onerror = () => {
                    alert('Upload failed due to a network error.');
                };

                xhr.send(formData);
            }

            e.stopPropagation();
            
            console.log(files);
        }, '#fl-upload-files');


        // Make folder / create directory
        this.lib('body').on('click', e => {
            e.preventDefault();

            const button = e.target.closest('button');
            const currentDirectory = button.getAttribute('data-path');

            // Create folder name / directory name
            let folderName = prompt('Folder name');
            if (!folderName) return alert('You must add a name to the directory!');
            folderName = folderName.toLowerCase();
            folderName = folderName.trim();
            folderName = folderName.replace(/\s+/g, '_');
            folderName = folderName.replace(/[^a-z0-9_-]/g, '');

            const url = `${this.variables.baseurl}/filemanager/create-directory`;
            const csrfToken = document.querySelector('[name="csrf_token"]').value;

            fetch(url, {
                method: 'POST',
                body: JSON.stringify({dirname: folderName, csrf_token: csrfToken, current_directory: currentDirectory}),
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "Content-Type": "text/html",
                },
            })
            .then(response => response.json())
            .then(data => {

                // Regenerate file manager
                this.functions.renderFileManager.call(this, this.variables.filemanagerDirectory, function(html) {
                    const modalElement = document.querySelector('#filemanager').closest('.uk-modal').querySelector('.uk-modal-body');
                    modalElement.innerHTML = html;  
                });

            }).catch(error => {
                console.error('Upload error:', error);
            })
            


        }, '.fl-create-folder');


        // Selecting / checking items & directories
        this.lib('body').on('change', e => {

            this.functions.selectItems.call(this, e);
            document.querySelector(this.selectors.fileManagerContainer).classList.add('active');

            if (this.variables.selectedIndexes.length > 1) {
                document.querySelector(this.selectors.fileManagerInfo).innerHTML = `Selected ${this.variables.selectedIndexes.length} items.`;
            } else if (this.variables.selectedIndexes.length == 1) {
                this.functions.getFileInfo.call(this, this.variables.selectedIndexes[0]);
            } else {
                document.querySelector(this.selectors.fileManagerInfo).innerHTML = '';
                document.querySelector(this.selectors.fileManagerContainer).classList.remove('active');
            }

        }, 'input.item-select-input')


        // Delete items & directories
        this.lib('body').on('click', e => {

            e.preventDefault();

            if (!this.variables.selectedIndexes.length) return alert('Select items first.');

            if (confirm('Are you sure?!')) {

                this.functions.deleteFiles.call(this);
            }
            
            
            this.variables.selectedIndexes = [];
            document.querySelectorAll('.fl-item-select').forEach(item => item.checked = false);
            let removeBtn = document.querySelector('#fl-remove-selected');
            let insertButton = document.querySelector('#fl-insert-button');
            
            removeBtn.classList.remove('active');
            removeBtn.classList.remove('fl-background-success');
            
            insertButton.classList.remove('active');
            insertButton.classList.remove('fl-background-success');
        }, '#fl-remove-selected.active');


        this.lib('body').on('click', this.functions.getImage.bind(this), '#fl-insert-button.active');
        

        // Open PDF file in new tab
        this.lib('body').on('click', e => {
            e.preventDefault();
            
            const link = e.target.getAttribute('data-href');
            if (link) window.open(link, '_blank');
        }, '.fl-item-document');


        // Open audio file
        this.lib('body').on('click', e => {
            e.preventDefault();

            const linkToFile = e.target.getAttribute('data-href');
            if (linkToFile) window.open(linkToFile, '', "width=800,height=570");

        }, '.fl-item-audio');


        // Compress to zip
        this.lib('body').on('click', e => {

            e.preventDefault();
            console.log(this.variables.compressionAction);

            // Zip / compress selected items
            this.functions.filesCompression.call(this);

        }, this.selectors.compressSelecetedButton);


        // Unzip archive
        this.lib('body').on('click', e => {            

            if (this.variables.compressionAction !== 'unzip') return;

            // Send AJAX request to unzip the archive
            e.preventDefault();

            if (!this.variables.selectedIndexes.length) return alert('Select items first.');

            const selectedItems = this.variables.selectedIndexes;
            const currentDirectory = document.querySelector('[name="filemanager_path"]').value;
            const csrfToken = document.querySelector('[name="csrf_token"]').value;

            document.querySelector('#loading-animation').classList.remove('uk-hidden');
            document.querySelector('#fl-loading-progress').innerText = 'Extracting archive. Please wait...';

            fetch(`${this.variables.baseurl}/filemanager/unzip`, {
                method: 'POST',
                body: JSON.stringify({
                    zip_file: selectedItems,
                    current_directory: currentDirectory,
                    csrf_token: csrfToken
                }),
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "Content-Type": "application/json",
                },
            })
            .then(response => response.json())
            .then(data => {
                document.querySelector('#loading-animation').classList.add('uk-hidden');
                document.querySelector('#fl-loading-progress').innerText = 'Unziping archive. Please wait...';
                if (data.success) {
                    UIkit.notification(`<p class="uk-margin-remove uk-text-small">${data.success}</p>`, {pos: 'bottom-center'});
                    this.functions.renderFileManager.call(this, this.variables.filemanagerDirectory, function(html) {
                        const modalElement = document.querySelector('#filemanager').closest('.uk-modal').querySelector('.uk-modal-body');
                        modalElement.innerHTML = html;
                    });
                }
            })
            .catch(error => {
                document.querySelector('#loading-animation').classList.add('uk-hidden');
                document.querySelector('#fl-loading-progress').innerText = '';
                console.error('Error:', error);
            });
            

        }, this.selectors.compressSelecetedButton);


        // Select all button
        this.lib('body').on('click', e => {
            e.preventDefault();

            this.variables.compressionAction = 'zip';

            // Toggle active classese
            let isTrue = false;
            e.target.closest('button').querySelectorAll('.check').forEach(el => isTrue = !el.classList.toggle('active'));
            e.target.closest('button').querySelectorAll('.uncheck').forEach(el => el.classList.toggle('active'));

            // Check or uncheck all items
            if (isTrue) {
                document.querySelectorAll('.fl-item-select').forEach(item => item.checked = true);
            } else {
                document.querySelectorAll('.fl-item-select').forEach(item => item.checked = false);
            }

            this.functions.selectItems.call(this, e);

            if (this.variables.selectedIndexes.length > 1) {
                document.querySelector(this.selectors.fileManagerContainer).classList.add('active');
                document.querySelector(this.selectors.fileManagerInfo).innerHTML = `Selected ${this.variables.selectedIndexes.length} items.`;
            } else {
                document.querySelector(this.selectors.fileManagerInfo).innerHTML = '';
                document.querySelector(this.selectors.fileManagerContainer).classList.remove('active');
            }
            
        }, '#fl-select-all-button');


        // Rename file or directory
        this.lib('body').on('click', e => {
            e.preventDefault();

            this.functions.renameFiles.call(this);

            e.stopPropagation();
        }, this.selectors.renameItemButton);

    }


    functions = {


        clearSearch() {
            this.functions.renderFileManager.call(this, this.variables.filemanagerDirectory, function(html) {
                const modalElement = document.querySelector('#filemanager').closest('.uk-modal').querySelector('.uk-modal-body');
                modalElement.innerHTML = html;
            });
        },


        // Search
        search(e) {

            e.preventDefault();

            if (!e.target.value) return;

            document.querySelector(this.selectors.searchIcon).innerHTML = this.variables.closeSvg;

            // Start loading animation
            document.querySelector('#loading-animation').classList.remove('uk-hidden');
            document.querySelector('#fl-loading-progress').innerText = 'Searching. Please wait...';


            // Check if search is in local directories
            if (!this.variables.searchAll) {

                const files = document.querySelectorAll('.fl-item-main');
                const searchTerm = e.target.value.toLowerCase();

                files.forEach(file => {
                    const fileName = file.querySelector('.fl-item-title').innerText.toLowerCase();
                    if (fileName.includes(searchTerm)) {
                        file.classList.remove('uk-hidden');
                    } else {
                        file.classList.add('uk-hidden');
                    }
                });

                // Loading animation stop
                document.querySelector('#loading-animation').classList.add('uk-hidden');
                document.querySelector('#fl-loading-progress').innerText = '';

                return;
            }

            // Search all directoreis
            // Send AJAX request to get filtered items
            fetch(`${this.variables.baseurl}/filemanager/search?file_name=${e.target.value}&current_directory=${document.querySelector('[name="filemanager_path"]').value}`, {
                method: 'GET',
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                },
            })
            .then(response => response.text())
            .then(data => {

                const modalElement = document.querySelector('#filemanager').closest('.uk-modal').querySelector('.uk-modal-body');
                modalElement.innerHTML = data;

                // Loading animation stop
                document.querySelector('#loading-animation').classList.add('uk-hidden');
                document.querySelector('#fl-loading-progress').innerText = '';
            })
            .catch(error => {
                console.error('Error:', error);

                // Loading animation stop
                document.querySelector('#loading-animation').classList.add('uk-hidden');
                document.querySelector('#fl-loading-progress').innerText = '';
            });
        },

        deleteFiles(fileNameArg = undefined) {
            // Getting current directory / folder name
            const currentDirectory = document.querySelector('[name="filemanager_path"]').value;

            // Prepare CSRF Token
            const csrfToken = document.querySelector('[name="csrf_token"]').value;

            // Send AJAX request with current directory and items array (this.variables.selectedIndexes)
            fetch(`${this.variables.baseurl}/filemanager/delete`, {
                headers: {
                    "Content-Type": "application/json",
                    "X-Requested-With": "XMLHttpRequest"
                },
                method: 'POST',
                body: JSON.stringify({
                    csrf_token: csrfToken,
                    current_directory: currentDirectory,
                    items: fileNameArg ? [fileNameArg] : this.variables.selectedIndexes
                })
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    this.functions.renderFileManager.call(this, this.variables.filemanagerDirectory, function(html) {
                        const modalElement = document.querySelector('#filemanager').closest('.uk-modal').querySelector('.uk-modal-body');
                        modalElement.innerHTML = html;
                    });
                }
            })
            .catch(err => {
                console.error(err)
            });
        },


        // Trigger download
        triggerDownload(e) {
            e.preventDefault();


            if (!this.variables.selectedIndexes.length) return alert('Select items first.');

            // If only one item selected and it's a file, download it directly
            if (this.variables.selectedIndexes.length == 1) {
                const item = this.variables.selectedIndexes[0];
                const itemType = this.variables.selectedType;

                // If single file and not a folder
                if (itemType !== 'folder') {
                    // Download single file without compression
                    const currentDirectory = document.querySelector('[name="filemanager_path"]').value;

                 
                    const itemBaseName = item.includes('\\') ? item.split('\\').reverse()[0] : item.split('/').reverse()[0];
                    const link = document.createElement('a');
                    link.href = `${currentDirectory}/${itemBaseName}`;
                    link.download = itemBaseName;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    return;
                }


                // If single file is a folder
                if (itemType == 'folder') {
                    // Compress folder and download

                    this.functions.filesCompression.call(this, true);

                }

                return;
            }


            // Compress selected items and download
            if (this.variables.selectedIndexes.length > 1) {
                console.log('Mixed items');
                

                this.functions.filesCompression.call(this, true);
            }


            e.stopPropagation();
        },



        // Delete files
        renameFiles() {
            if (this.variables.selectedIndexes.length != 1) return alert(`You have ${this.variables.selectedIndexes.length} selected items. Select one item to rename!`);

            let oldName = this.variables.selectedIndexes[0];
            let newName = prompt('Enter new name', (oldName.includes('\\') ? oldName.split('\\').reverse()[0] : oldName.split('/').reverse()[0]));
            if (!newName) return alert('You must add a name!');

            const currentDirectory = document.querySelector('[name="filemanager_path"]').value;
            const csrfToken = document.querySelector('[name="csrf_token"]').value;
            document.querySelector('#loading-animation').classList.remove('uk-hidden');
            document.querySelector('#fl-loading-progress').innerText = 'Renaming file. Please wait...';

            fetch(`${this.variables.baseurl}/filemanager/rename`, {
                method: 'POST',
                body: JSON.stringify({old_name: oldName, new_name: newName, current_directory: currentDirectory, csrf_token: csrfToken}),
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "Content-Type": "application/json",
                },
            })
            .then(response => response.json())
            .then(data => {

                document.querySelector('#loading-animation').classList.add('uk-hidden');
                document.querySelector('#fl-loading-progress').innerText = 'Renaming file. Please wait...';
                if (data.success) {
                    UIkit.notification(`<p class="uk-margin-remove uk-text-small">${data.success}</p>`, {pos: 'bottom-center'});
                    this.functions.renderFileManager.call(this, this.variables.filemanagerDirectory, function(html) {
                        const modalElement = document.querySelector('#filemanager').closest('.uk-modal').querySelector('.uk-modal-body');
                        modalElement.innerHTML = html;  
                    });
                } else if (data.status && data.status == 'error') {
                    return alert(data.message);
                }
            }).catch(error => {
                document.querySelector('#loading-animation').classList.add('uk-hidden');
                document.querySelector('#fl-loading-progress').innerText = '';
                console.error('Error:', error);
            });
        },


        // Zip files // Compress files
        filesCompression(triggerDownload = false) {
            if (this.variables.compressionAction !== 'zip') return;

            if (!this.variables.selectedIndexes.length) return alert('Select items first.');

            const selectedItems = this.variables.selectedIndexes;
            const currentDirectory = document.querySelector('[name="filemanager_path"]').value;
            const csrfToken = document.querySelector('[name="csrf_token"]').value;

            document.querySelector('#loading-animation').classList.remove('uk-hidden');
            document.querySelector('#fl-loading-progress').innerText = 'Compressing files. Please wait...';

            fetch(`${this.variables.baseurl}/filemanager/compress`, {
                method: 'POST',
                body: JSON.stringify({items: selectedItems, current_directory: currentDirectory, csrf_token: csrfToken}),
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "Content-Type": "application/json",
                },
            })
            .then(response => response.json())
            .then(data => {
                document.querySelector('#loading-animation').classList.add('uk-hidden');
                document.querySelector('#fl-loading-progress').innerText = '';
                console.log(data);
                
                if (data.success) {
                    UIkit.notification(`<p class="uk-margin-remove uk-text-small">${data.success}</p>`, {pos: 'bottom-center'});
                    this.functions.renderFileManager.call(this, this.variables.filemanagerDirectory, function(html) {
                        const modalElement = document.querySelector('#filemanager').closest('.uk-modal').querySelector('.uk-modal-body');
                        modalElement.innerHTML = html;  
                    });


                    // Trigger download if needed
                    if (triggerDownload && data.archive) {
                        const link = document.createElement('a');
                        const href = data.archive;
                        link.href = href;
                        link.download = data.archive;

                        // console.log(link);
                        // return;
                        
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);

                        // Remove just compressed file
                        this.functions.deleteFiles.call(this, data.archive);
                    }
                }
            }).catch(error => {
                document.querySelector('#loading-animation').classList.add('uk-hidden');
                document.querySelector('#fl-loading-progress').innerText = '';
                console.error('Error:', error);
            });
        },



        applyFullscreenState() {
            const fullscreenBtn = document.querySelector(this.selectors.fullscreenButton);
            const flModal = document.querySelector(this.selectors.flModal);

            if (this.variables.isFullscreen) {
                flModal.classList.add('fl-fullscreen');
                fullscreenBtn.classList.add('fl-background-success');
            } else {
                flModal.classList.remove('fl-fullscreen');
                fullscreenBtn.classList.remove('fl-background-success');
            }
        },

        toggleFullscreen(e) {
            e.preventDefault();

            const isFullscreen = this.variables.isFullscreen = !this.variables.isFullscreen;
            localStorage.setItem('toolx_fl_fullscreen', isFullscreen);

            this.functions.applyFullscreenState.call(this);

            e.stopPropagation();
        },


        getFileInfo(files) {
            // const currentDirectory = document.querySelector('[name="filemanager_path"]').value;
            const csrfToken = document.querySelector('[name="csrf_token"]').value;

            fetch(`${this.variables.baseurl}/filemanager/file-info?item_name=${encodeURIComponent(files)}`, {
                method: 'GET',
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "Content-Type": "application/json",
                },
            })
            .then(response => response.json())
            .then(res => {
                if (res.status && res.status == 'error') {
                    return alert(res.message);
                }
                const container = document.querySelector(this.selectors.fileManagerInfo);
                container.innerHTML = `
                    <ul class="uk-list uk-text-small uk-list-collapse uk-margin-remove">
                        <li><strong>Name:</strong> ${res.data.name}</li>
                        <li><strong>Type:</strong> ${res.data.type}</li>
                        <li><strong>Size:</strong> ${res.data.size}</li>
                        <li><strong>Last Modified:</strong> ${res.data.last_modified}</li>
                        <li><strong>Path:</strong> ${res.data.path}</li>
                    </ul>
                `;
            }).catch(error => console.error('Error:', error));
        },


        // Working with file manager
        renderFileManager(url, callback) {

            this.variables.filemanagerDirectory = url;
            console.log(this.variables.filemanagerDirectory);
            
            fetch(url, {
                method: 'GET',
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "Content-Type": "text/html",
                },
            })
            .then(response => response.text())
            .then(html => {

                const interval = setInterval(() => {
                    if (html.length) {
                        document.querySelector('#loading-animation').classList.add('uk-hidden');
                        clearInterval(interval);
                    } else {
                        document.querySelector('#loading-animation').classList.remove('uk-hidden');
                        document.querySelector('#fl-loading-progress').innerText = 'Opening files... Please wait';
                    }
                }, 500);

                callback(html);

                if ( !localStorage.getItem('toolx_fl_fullscreen') ) {
                    this.variables.isFullscreen = true;
                    this.functions.applyFullscreenState.call(this);
                }

                if (localStorage.getItem('toolx_fl_fullscreen') === 'true') {
                    this.variables.isFullscreen = true;
                    this.functions.applyFullscreenState.call(this);
                }
                
            }).catch(error => console.error('Error:', error));
        },



        selectItems(e) {
            const checkSelectedItems = () => {
                return Array.from(document.querySelectorAll('.item-select-input:checked')).some(inp => {
                    if (this.variables.selectedIndexes.length == 1 && inp.getAttribute('data-type') === 'archive') {
                        this.variables.compressionAction = 'unzip';
                        this.variables.selectedType = 'file';
                    }
                    return inp.getAttribute('data-type') === 'file' || inp.getAttribute('data-type') === 'archive'
                });
            }

            this.variables.compressionAction = 'zip';
            // Collecting checked input indexes
            this.variables.selectedIndexes = Array.from(document.querySelectorAll('.item-select-input:checked')).map(inp => inp.value);
            const removeButton = document.querySelector('#fl-remove-selected');
            const insertButton = document.querySelector('#fl-insert-button');
            const compressButton = document.querySelector(this.selectors.compressSelecetedButton);
            const renameButton = document.querySelector(this.selectors.renameItemButton);
            const downloadButton = document.querySelector(this.selectors.downloadSelectedButton);
            
            if (this.variables.selectedIndexes.length) {
                removeButton.classList.add('fl-background-success', 'active');
                compressButton.classList.add('fl-background-success', 'active');
                const type = e.target.getAttribute('data-type');
                renameButton.classList.add('fl-background-success', 'active');
                downloadButton.classList.add('fl-background-success', 'active');
                
                if (type == 'file') {
                    insertButton.classList.add('fl-background-success', 'active');
                    this.variables.selectedType = 'file';
                }
                if (type == 'archive') {
                    if (this.variables.selectedIndexes.length == 1) this.variables.compressionAction = 'unzip';
                    insertButton.classList.add('fl-background-success', 'active');
                }
            } else {
                removeButton.classList.remove('fl-background-success');
                removeButton.classList.remove('active');

                compressButton.classList.remove('fl-background-success');
                compressButton.classList.remove('active');

                renameButton.classList.remove('active');
                renameButton.classList.remove('fl-background-success');

                downloadButton.classList.remove('fl-background-success', 'active');

                // insertButton.classList.remove('fl-background-success');
                // insertButton.classList.remove('active');
            }

            if (this.variables.selectedIndexes.length > 1) {
                this.variables.compressionAction = 'zip';
                this.variables.selectedType = 'mixed';
            }

            if (!checkSelectedItems()) {
                insertButton.classList.remove('fl-background-success');
                insertButton.classList.remove('active');

                this.variables.selectedType = 'folder';
            }


            if (this.variables.selectedIndexes.length < 1) {
                this.variables.selectedType = undefined;
            }

            console.log(this.variables.compressionAction);
            console.log(this.variables.selectedType);
            
        },
        
        
        
        // Filemanager get image url and directory path
        getImage(e, callback) {

            let image = this.variables.selectedIndexes.filter(item => {
                if (typeof item === 'string') {
                    // Check if the string ends with a dot and 3 to 5 letters (file extension)
                    if (/\.[a-zA-Z0-9]{3,5}$/.test(item)) {
                        return item;
                    }
                }
                return null;
            });

            if (!image.length) return alert('Select image first');

            const dirPath = e.target.closest('button').getAttribute('data-path');
            const tinymceImageInput = document.querySelector('.tox-dialog__body input');
            const imageLink = `${dirPath}/${image[0]}`;
            tinymceImageInput.value = imageLink;

            UIkit.modal('#filemanager-modal').hide();

            // callback({files: this.variables.selectedIndexes, dirPath});
        },


        tinymceInit()
        {
            tinymce.init({
                selector: this.selectors.tinyArea,
                plugins: 'preview importcss searchreplace autolink autosave save directionality visualblocks visualchars fullscreen image link media codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons',
                toolbar: 'undo redo | bold italic underline strikethrough | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist checklist | forecolor backcolor casechange permanentpen formatpainter removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media pageembed template link anchor codesample | a11ycheck ltr rtl | showcomments addcomment',
                
                
                // image_advtab: true
                file_picker_callback: (callback, value, meta) => {

                    document.body.insertAdjacentHTML('beforeend', `<div id="dom-loader-animation">
                        <div class="spinner"></div>
                    </div>`);

                    if (!this.variables.filemanagerDirectory) this.variables.filemanagerDirectory = this.variables.baseurl + '/filemanager';

                    // callback(imgLink, { title: imageName, alt: imageName }); // Working with tinymce callback

                    // Opening file manager
                    this.functions.renderFileManager.call(this, `${this.variables.baseurl}/filemanager`, (html) => {

                        document.getElementById('dom-loader-animation').remove();
                        
                        const dialog = UIkit.modal.dialog(html);
                        const modalElement = dialog.$el;
                        modalElement.id = 'filemanager-modal';
                        
                        this.functions.applyFullscreenState.call(this);

                        modalElement.classList.add('uk-modal-container');
                        modalElement.querySelector('.uk-modal-dialog').className = 'uk-modal-container uk-modal-dialog uk-modal-body uk-margin-auto-vertical uk-border-rounded';
                        // modalElement.querySelector('.uk-modal-dialog').insertAdjacentHTML('afterbegin', `<button class="uk-modal-close-default" type="button" uk-close></button>`);
                        
                    });

                }
            });
        },

    }

}