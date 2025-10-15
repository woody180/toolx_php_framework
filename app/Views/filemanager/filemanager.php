<div id="filemanager-toolbar" class="uk-flex uk-flex-between uk-flex-middle uk-background-muted uk-padding-small uk-border-rounded">
    <!-- <p class="uk-text-lead uk-margin-remove">File manager</p> -->
    <div class="uk-flex uk-flex-between uk-width-1-1">

        <div class="uk-flex-1 uk-flex uk-flex-middle">
            
            <form class="uk-search uk-search-default uk-margin-small-right">
                <input id="fl-manager-items-search" class="uk-search-input uk-border-rounded" type="search" placeholder="Search" aria-label="Search" value="<?= query('file_name') ?>">
                <a uk-tooltip="Clear search and results" href="#" class="uk-search-icon-flip uk-icon uk-search-icon">
                    <span id="fl-clear-search-icon">
                        <?php if ($deep_search === TRUE): ?>
                            <svg width="25" height="25" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="Menu / Close_SM"><path id="Vector" d="M16 16L12 12M12 12L8 8M12 12L16 8M12 12L8 16" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></g></svg>
                        <?php else: ?>
                            <svg width="20" height="20" viewBox="0 0 20 20"><circle fill="none" stroke="#000" stroke-width="1.1" cx="9" cy="9" r="7"></circle><path fill="none" stroke="#000" stroke-width="1.1" d="M14,14 L18,18 L14,14 Z"></path></svg>
                        <?php endif; ?>
                    </span>
                </a>
            </form>

            <label class="uk-switch" for="fl-search-all" uk-tooltip="Search in all directories.">
                <input type="checkbox" id="fl-search-all" <?= $deep_search === TRUE ? 'checked' : '' ?>>
                <div class="uk-switch-slider"></div>
            </label>

        </div>

        <div class="uk-flex">
            <div class="uk-margin-small-right">
                <button class="uk-button uk-button-default uk-border-rounded fl-insert-button" id="fl-insert-button" data-path="<?= $this->e($currentUrl) ?>" uk-tooltip="pos: top; title: Insert selected item(s)" title="Insert selected item(s)">
                    <span uk-icon="icon: check; ratio: .8"></span>
                     &nbsp;<span>Insert</span>
                </button>
            </div>

            <button class="uk-button uk-button-default uk-border-rounded uk-margin-small-right" id="fl-select-all-button" uk-tooltip="pos: top; title: Select or unselect all items" title="Select or unselect all items">
                <span class="uk-position-relative check active">
                    <svg xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 50 50" width="16px" height="16px" style="position: relative; top: -1px; margin-right: 1px;"><path d="M 25 2 C 12.309534 2 2 12.309534 2 25 C 2 37.690466 12.309534 48 25 48 C 37.690466 48 48 37.690466 48 25 C 48 12.309534 37.690466 2 25 2 z M 25 4 C 36.609534 4 46 13.390466 46 25 C 46 36.609534 36.609534 46 25 46 C 13.390466 46 4 36.609534 4 25 C 4 13.390466 13.390466 4 25 4 z M 34.988281 14.988281 A 1.0001 1.0001 0 0 0 34.171875 15.439453 L 23.970703 30.476562 L 16.679688 23.710938 A 1.0001 1.0001 0 1 0 15.320312 25.177734 L 24.316406 33.525391 L 35.828125 16.560547 A 1.0001 1.0001 0 0 0 34.988281 14.988281 z"/></svg>
                </span>
                <span class="uk-position-relative uncheck">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 50" width="16px" height="16px" style="position: relative; top: -1px;"><path d="M25,2C12.318,2,2,12.318,2,25c0,12.683,10.318,23,23,23c12.683,0,23-10.317,23-23C48,12.318,37.683,2,25,2z M35.827,16.562 L24.316,33.525l-8.997-8.349c-0.405-0.375-0.429-1.008-0.053-1.413c0.375-0.406,1.009-0.428,1.413-0.053l7.29,6.764l10.203-15.036 c0.311-0.457,0.933-0.575,1.389-0.266C36.019,15.482,36.138,16.104,35.827,16.562z"/></svg>
                </span>
                 &nbsp; <span class="check active">Select &nbsp;</span><span class="uncheck" >Diselect &nbsp;</span> all
            </button>

            <button class="uk-button uk-button-default uk-border-rounded uk-margin-small-right" id="fl-compress-selected" data-path="<?= $this->e($currentUrl) ?>"  uk-tooltip="pos: top; title: Compress / Uncompress" title="Compress selected items to ZIP archive">
                <svg width="17px" height="17px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="none" stroke="#333" stroke-width="2" d="M4.99787498,8.99999999 L4.99787498,0.999999992 L19.4999998,0.999999992 L22.9999998,4.50000005 L23,23 L4,23 M18,1 L18,6 L23,6 M2,13 L7,13 L7,14 L3,18 L3,19 L8,19 M11,12 L11,20 L11,12 Z M15,13 L15,20 L15,13 Z M20,15 C20,13.895 19.105,13 18,13 L15,13 L15,17 L18,17 C19.105,17 20,16.105 20,15 Z"/></svg>
            </button>

            <button class="uk-button uk-button-default uk-border-rounded uk-margin-small-right" id="fl-download-selected" uk-tooltip="pos: top; title: Download selected items" title="Download selected items">
                <span uk-icon="icon: download; ratio: .95"></span>
            </button>

            <div class="uk-margin-small-right">
                <button class="uk-button uk-button-default uk-border-rounded" id="fl-remove-selected" uk-tooltip="pos: top; title: Delete selected items" title="Delete selected items">
                    <span uk-icon="icon: trash; ratio: .85"></span></button>
            </div>

            <button class="uk-button uk-button-default uk-border-rounded uk-margin-small-right" id="fl-fullscreen-button"  uk-tooltip="pos: top; title: Toggle fullscreen" title="Toggle fullscreen">
                <svg fill="#000000" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid" width="16px" height="16px"viewBox="0 0 31.812 31.906"><path d="M31.728,31.291 C31.628,31.535 31.434,31.729 31.190,31.830 C31.069,31.881 30.940,31.907 30.811,31.907 L23.851,31.907 C23.301,31.907 22.856,31.461 22.856,30.910 C22.856,30.359 23.301,29.913 23.851,29.913 L28.405,29.908 L19.171,20.646 C18.782,20.257 18.782,19.626 19.171,19.236 C19.559,18.847 20.188,18.847 20.577,19.236 L29.906,28.593 L29.906,23.906 C29.906,23.355 30.261,22.933 30.811,22.933 C31.360,22.933 31.805,23.379 31.805,23.930 L31.805,30.910 C31.805,31.040 31.779,31.169 31.728,31.291 ZM30.811,8.973 C30.261,8.973 29.906,8.457 29.906,7.906 L29.906,3.313 L20.577,12.669 C20.382,12.864 20.128,12.962 19.874,12.962 C19.619,12.962 19.365,12.864 19.171,12.669 C18.782,12.280 18.782,11.649 19.171,11.259 L28.497,1.906 L23.906,1.906 C23.356,1.906 22.856,1.546 22.856,0.996 C22.856,0.445 23.301,-0.001 23.851,-0.001 L30.811,-0.001 C30.811,-0.001 30.811,-0.001 30.812,-0.001 C30.941,-0.001 31.069,0.025 31.190,0.076 C31.434,0.177 31.628,0.371 31.728,0.615 C31.779,0.737 31.805,0.866 31.805,0.996 L31.805,7.976 C31.805,8.526 31.360,8.973 30.811,8.973 ZM3.387,29.908 L7.942,29.913 C8.492,29.913 8.936,30.359 8.936,30.910 C8.936,31.461 8.492,31.907 7.942,31.907 L0.982,31.907 C0.853,31.907 0.724,31.881 0.602,31.830 C0.359,31.729 0.165,31.535 0.064,31.291 C0.014,31.169 -0.012,31.040 -0.012,30.910 L-0.012,23.930 C-0.012,23.379 0.433,22.933 0.982,22.933 C1.532,22.933 1.906,23.355 1.906,23.906 L1.906,28.573 L11.216,19.236 C11.605,18.847 12.234,18.847 12.622,19.236 C13.011,19.626 13.011,20.257 12.622,20.646 L3.387,29.908 ZM11.919,12.962 C11.665,12.962 11.410,12.864 11.216,12.669 L1.906,3.332 L1.906,7.906 C1.906,8.457 1.532,8.973 0.982,8.973 C0.433,8.973 -0.012,8.526 -0.012,7.976 L-0.012,0.996 C-0.012,0.866 0.014,0.737 0.064,0.615 C0.165,0.371 0.359,0.177 0.602,0.076 C0.723,0.025 0.852,-0.001 0.980,-0.001 C0.981,-0.001 0.981,-0.001 0.982,-0.001 L7.942,-0.001 C8.492,-0.001 8.936,0.445 8.936,0.996 C8.936,1.546 8.456,1.906 7.906,1.906 L3.296,1.906 L12.622,11.259 C13.011,11.649 13.011,12.280 12.622,12.669 C12.428,12.864 12.174,12.962 11.919,12.962 Z"/></svg>
            </button>
            
            <button class="uk-button uk-button-default uk-border-rounded uk-margin-small-right" id="fl-rename-item" uk-tooltip="pos: top; title: Rename selected item" title="Rename selected item">
                <svg width="17px" height="17px" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M11.75 2C11.3358 2 11 2.33579 11 2.75C11 3.16421 11.3358 3.5 11.75 3.5H13.25V24.5H11.75C11.3358 24.5 11 24.8358 11 25.25C11 25.6642 11.3358 26 11.75 26H16.25C16.6642 26 17 25.6642 17 25.25C17 24.8358 16.6642 24.5 16.25 24.5H14.75V3.5H16.25C16.6642 3.5 17 3.16421 17 2.75C17 2.33579 16.6642 2 16.25 2H11.75Z" fill="#212121"/><path d="M6.25 6.01958H12.25V7.51958H6.25C5.2835 7.51958 4.5 8.30308 4.5 9.26958V18.7696C4.5 19.7361 5.2835 20.5196 6.25 20.5196H12.25V22.0196H6.25C4.45507 22.0196 3 20.5645 3 18.7696V9.26958C3 7.47465 4.45507 6.01958 6.25 6.01958Z" fill="#212121"/><path d="M21.75 20.5196H15.75V22.0196H21.75C23.5449 22.0196 25 20.5645 25 18.7696V9.26958C25 7.47465 23.5449 6.01958 21.75 6.01958H15.75V7.51958H21.75C22.7165 7.51958 23.5 8.30308 23.5 9.26958V18.7696C23.5 19.7361 22.7165 20.5196 21.75 20.5196Z" fill="#212121"/></svg>
            </button>

            <div>
                <button class="uk-button uk-button-default uk-border-rounded uk-margin-small-right fl-create-folder" data-path="<?= $this->e($currentUrl) ?>" uk-tooltip="pos: top; title: Create new folder" title="Create new folder">
                    <span uk-icon="icon: plus-circle; ratio: .75"></span>
                     &nbsp;<span>Folder</span>
                </button>
            </div>

            <form action="<?= baseUrl('filemanager/upload') ?>" method="POST" enctype="multipart/form-data" id="fl-upload-form">
                <?= csrf_field() ?>
                <label for="fl-upload-files" class="uk-button uk-button-default uk-border-rounded" uk-tooltip="pos: top; title: Upload files" title="Upload files">
                    <span uk-icon="icon: upload; ratio: .8" style="position: relative; top: -1px"></span>  &nbsp;<span>Upload</span>
                    <input type="file" name="images[]" multiple hidden="true" id="fl-upload-files" />
                    <input type="hidden" name="filemanager_path" value="<?= $this->e($currentUrl) ?>" />
                </label>
            </form>

            <a class="uk-button uk-button-default uk-border-rounded uk-margin-small-left fl-back-dir-button" href="<?= $backUrl ?>" uk-tooltip="pos: top; title: Go back to previous directory" title="Go back to previous directory">
                <span uk-icon="icon: arrow-left; ratio: 1" style="position: relative; top: -1px"></span><span style="position: relative; top: -1px">&nbsp;Back</span></a>


            <button class="uk-modal-close-default" type="button" uk-close></button>
        </div>
    </div>
</div>

<div id="fl-modal-data">
    <div class="uk-container uk-container-expand">
    <div uk-grid class="uk-child-width-auto" id="filemanager" uk-lightbox>

        <?php foreach($items as $index => $item): ?>
            <?php if($item['type'] === 'folder'): ?>
                <div class="uk-position-relative fl-item-main">
                    <input data-type="folder" class="fl-item-select uk-checkbox item-select-input" type="checkbox" value="<?= $this->e($item['path']) ?>">
                    <div class="fl-item" data-index="<?= $index ?>">
                        <div class="fl-item-dir" data-href="<?= CURRENT_URL . '/' . $this->e($item['name']) ?>" title="<?= $this->e($item['name']) ?>"></div>
                        <span class="fl-item-title" title="<?= $this->e($item['name']) ?>"><?= $this->e($item['name']) ?></span>
                    </div>
                </div>
            <?php elseif($item['type'] === 'file'): /* This is for images */ ?>
                <div class="uk-position-relative fl-item-main">
                    <input data-type="file" class="fl-item-select uk-checkbox item-select-input" type="checkbox" value="<?= $this->e($item['path']) ?>">
                    <a data-index="<?= $index ?>" href="<?= $item['path'] ?>" class="fl-item">
                        <img class="fl-item-image" src="<?= $this->e($item['cachedPath']) ?>" alt="<?= $this->e($item['name']) ?>" title="<?= $this->e($item['name']) ?>" />
                        <span class="fl-item-title" title="<?= $this->e($item['name']) ?>"><?= $this->e($item['name']) ?></span>
                    </a>
                </div>
            <?php elseif($item['type'] === 'audio'): ?>
                <div class="uk-position-relative fl-item-main">
                    <input data-type="file" class="fl-item-select uk-checkbox item-select-input" type="checkbox" value="<?= $this->e($item['path']) ?>">
                    <div class="fl-item" data-index="<?= $index ?>">
                        <div class="fl-item-audio" data-href="<?= $currentUrl . '/' . $this->e($item['name']) ?>" title="<?= $this->e($item['name']) ?>"></div>
                        <span class="uk-link fl-item-title" title="<?= $this->e($item['name']) ?>"><?= $this->e($item['name']) ?></span>
                    </div>
                </div>
            <?php elseif($item['type'] === 'document'): ?>
                <div class="uk-position-relative fl-item-main">
                    <input data-type="file" class="fl-item-select uk-checkbox item-select-input" type="checkbox" value="<?= $this->e($item['path']) ?>">
                    <div class="fl-item" data-index="<?= $index ?>">
                        <div data-href="<?= $currentUrl . '/' . $this->e($item['name']) ?>" class="fl-item-document" title="<?= $this->e($item['name']) ?>"></div>
                        <span class="uk-link fl-item-title" title="<?= $this->e($item['name']) ?>"><?= $this->e($item['name']) ?></span>
                    </div>
                </div>
            <?php elseif($item['type'] === 'video'): ?>
                <div class="uk-position-relative fl-item-main">
                    <input data-type="file" class="fl-item-select uk-checkbox item-select-input" type="checkbox" value="<?= $this->e($item['path']) ?>">
                    <a data-index="<?= $index ?>" href="<?= $currentUrl . '/' . $this->e($item['name']) ?>" class="fl-item">
                        <div class="fl-item-video" data-href="<?= CURRENT_URL . '/' . $this->e($item['name']) ?>" title="<?= $this->e($item['name']) ?>"></div>
                        <span class="fl-item-title" title="<?= $this->e($item['name']) ?>"><?= $this->e($item['name']) ?></span>
                    </a>
                </div>
            <?php elseif($item['type'] === 'archive'): ?>
                <div class="uk-position-relative fl-item-main">
                    <input data-type="archive" class="fl-item-select uk-checkbox item-select-input" type="checkbox" value="<?= $this->e($item['path']) ?>">
                    <div data-index="<?= $index ?>" href="<?= $currentUrl . '/' . $this->e($item['name']) ?>" class="fl-item">
                        <div class="fl-item-archive" data-href="<?= CURRENT_URL . '/' . $this->e($item['name']) ?>" title="<?= $this->e($item['name']) ?>"></div>
                        <span class="fl-item-title" title="<?= $this->e($item['name']) ?>"><?= $this->e($item['name']) ?></span>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>

    </div>
</div>



<div id="filemanager-directory-prompt" class="uk-modal" uk-modal>
    <div class="uk-modal-dialog uk-modal-body">
        <h2 class="uk-modal-title">Folder name:</h2>
        <input type="text" class="uk-input uk-border-rounded" placeholder="Folder name">
        <div class="uk-modal-footer">
            <button class="uk-button uk-button-default" type="button" uk-modal-close>Cancel</button>
            <button class="uk-button uk-button-primary" id="submitName">OK</button>
        </div>
    </div>
</div>


<div id="loading-animation" class="">
    <div>
        <div class="spinner"></div>
        <p><span id="fl-loading-progress">Opening files... Please wait</span></p>
    </div>
</div>
</div>


<div id="file-information-container">
    <div class="uk-position-relative">
        <a href="" class="uk-icon-button uk-position-top-right" id="fl-close-info-bar" uk-icon="close"></a>
        <div class="uk-margin-remove" id="fl-file-information">
            something is here
        </div>
    </div>
</div>


<div id="fl-server-info" class="uk-position-bottom-left uk-background-muted uk-width-1-1 uk-flex uk-flex-between uk-flex-middle">
    <!-- <progress id="js-progressbar" class="uk-progress uk-margin-small-bottom uk-hidden" value="10" max="100"></progress> -->
    <p class="uk-text-muted uk-margin-remove">
        <?php
        $maxFileUploads = ini_get('max_file_uploads');
        $maxPostSize = ini_get('post_max_size');
        $maxUploadSize = ini_get('upload_max_filesize');
        echo "Maximum files per upload: {$maxFileUploads}. ";
        echo "Maximum upload size: {$maxUploadSize}. ";
        echo "Maximum POST size: {$maxPostSize}.";
        ?>
    </p>
</div>