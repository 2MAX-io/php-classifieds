"use strict";

var saveEditedImage = function(image, item) {
    // set new image
    item.editor._blob = image;

    // if still uploading
    // pend and exit
    if (item.upload && item.upload.status === 'loading')
        return item.editor.isUploadPending = true;

    // if not uploaded
    if (item.upload && item.upload.send && !item.upload.status) {
        item.editor._namee = item.name;
        return item.upload.send();
    }

    // if not preloaded or not uploaded
    if (!item.appended && !item.uploaded)
        return;

    // if no editor
    if (!item.editor || !item.reader.width)
        return;

    // if uploaded
    // resend upload
    if (item.upload && item.upload.resend) {
        item.editor._namee = item.name;
        item.editor._editingg = true;
        item.upload.resend();
    }

    // if preloaded
    // send request
    if (item.appended) {
        // hide current thumbnail (this is only animation)
        item.imageIsUploading = true;
        item.editor._editingg = true;

        var form = new FormData();
        form.append('files[]', item.editor._blob);
        form.append('fileuploader', '1');
        form.append('_namee', item.name);
        form.append('_editingg', '1');

        $.ajax({
            url: Routing.generate('app_file_upload'),
            data: form,
            type: 'POST',
            processData: false,
            contentType: false
        });
    }
};

$('#listing_file').fileuploader({
    fileMaxSize: 40,
    extensions: ['jpg', 'jpeg', 'png', 'gif'],
    limit: 10,
    addMore: true,
    enableApi: true,
    files: app.getJsonDataCached()['listingFilesForJavascript'],
    thumbnails: {
        popup: {
            onShow: function(item) {
                item.popup.html.on('click', '[data-action="remove"]', function() {
                    item.popup.close();
                    item.remove();
                }).on('click', '[data-action="cancel"]', function() {
                    item.popup.close();
                }).on('click', '[data-action="save"]', function() {
                    if (item.editor)
                        item.editor.save(function(blob, item) {
                            saveEditedImage(blob, item);
                        }, true, null, false);

                    if (item.popup.close)
                        item.popup.close();
                });
            }
        },
        onItemShow: function(item) {
            // add sorter button to the item html
            item.html.find('.fileuploader-action-remove').before('<a class="fileuploader-action fileuploader-action-sort" title="Sort"><i></i></a>');
        },
        onImageLoaded: function(item) {
            // if (!item.html.find('.fileuploader-action-edit').length)
            //     item.html.find('.fileuploader-action-remove').before('<a class="fileuploader-action fileuploader-action-popup fileuploader-action-edit" title="Edit"><i></i></a>');

            if (item.appended)
                return;

            // hide current thumbnail (this is only animation)
            if (item.imageIsUploading) {
                item.image.addClass('fileuploader-loading').html('');
            }

            if (!item.imageLoaded)
                item.editor.save(function(blob, item) {
                    saveEditedImage(blob, item);
                }, true, null, true);

            item.imageLoaded = true;
        }
    },
    upload: {
        url: Routing.generate('app_file_upload'),
        data: null,
        type: 'POST',
        enctype: 'multipart/form-data',
        start: false,
        synchron: true,
        beforeSend: function(item, listEl, parentEl, newInputEl, inputEl) {
            // add image to formData
            if (item.editor && item.editor._blob) {
                item.upload.data.fileuploader = 1;
                if (item.upload.formData.delete)
                    item.upload.formData.delete(inputEl.attr('name'));
                item.upload.formData.append(inputEl.attr('name'), item.editor._blob, item.name);

                // add name to data
                if (item.editor._namee) {
                    item.upload.data._namee = item.name;
                }

                // add is after editing to data
                if (item.editor._editingg) {
                    item.upload.data._editingg = true;
                }
            }

            item.html.find('.fileuploader-action-success').removeClass('fileuploader-action-success');
        },
        onSuccess: function(data, item) {
            // if success
            if (data.isSuccess && data.files[0]) {
                item.data.tmpFilePath = data.files[0].file;
                item.name = data.files[0].old_name;
                item.html.find('.column-title > div:first-child').text(item.name).attr('title', item.name);

                // send pending editor
                if (item.editor && item.editor.isUploadPending) {
                    delete item.editor.isUploadPending;

                    saveEditedImage(item.editor._blob, item);
                }
            }

            // if warnings
            if (data.hasWarnings) {
                for (var warning in data.warnings) {
                    alert(data.warnings);
                }

                item.html.removeClass('upload-successful').addClass('upload-failed');
                // go out from success function by calling onError function
                // in this case we have a animation there
                // you can also response in PHP with 404
                return this.onError ? this.onError(item) : null;
            }

            item.html.find('.fileuploader-action-remove').addClass('fileuploader-action-success');
            setTimeout(function() {
                item.html.find('.progress-bar2').fadeOut(400);
            }, 400);
        },
        onError: function(item) {
            var progressBar = item.html.find('.progress-bar2');

            if(progressBar.length) {
                progressBar.find('span').html(0 + "%");
                progressBar.find('.fileuploader-progressbar .bar').width(0 + "%");
                item.html.find('.progress-bar2').fadeOut(400);
            }

            item.upload.status !== 'cancelled' && item.html.find('.fileuploader-action-retry').length === 0 ? item.html.find('.column-actions').prepend(
                '<a class="fileuploader-action fileuploader-action-retry" title="Retry"><i></i></a>'
            ) : null;
        },
        onProgress: function(data, item) {
            var progressBar = item.html.find('.progress-bar2');

            if(progressBar.length > 0) {
                progressBar.show();
                progressBar.find('span').html(data.percentage + "%");
                progressBar.find('.fileuploader-progressbar .bar').width(data.percentage + "%");
            }
        },
        onComplete: null
    },
    onListInput: function(list, fileList) {
        list.forEach(function (el, i) {
            el.name = fileList[i].name;
            el.type = fileList[i].type;
            el.size = fileList[i].size;
            el.data = fileList[i].data;
        });

        return list;
    },
    onRemove: function(item) {
        if ('listingFileId' in item.data) {
            $.post(Routing.generate('app_listing_file_remove'), {
                listingFileId: item.data.listingFileId
            });
        }

        return true;
    },
    sorter: {
        selectorExclude: null,
        placeholder: null,
        scrollContainer: window,
        onSort: function(list, listEl, parentEl, newInputEl, inputEl) {}
    },
    editor: {
        cropper: {
            showGrid: true
        },
        maxWidth: 1920,
        maxHeight: 1080,
        quality: 90
    },
    reader: {
        maxSize: 50
    },
    captions: {
        button: function(options) { return Translator.trans('trans.Browse') + ' ' + (options.limit === 1 ? Translator.trans('trans.file') : Translator.trans('trans.files')); },
        feedback: function(options) { return Translator.trans('trans.Chose') + ' ' + (options.limit === 1 ? Translator.trans('trans.file') : Translator.trans('trans.files')) + ' ' + Translator.trans('trans.to upload'); },
        feedback2: function(options) { return options.length + ' ' + (options.length > 1 ? ' ' + Translator.trans('trans.files were chosen') : ' ' + Translator.trans('trans.file was chosen')); },
        confirm: Translator.trans('trans.Confirm'),
        cancel: Translator.trans('trans.Cancel'),
        name: Translator.trans('trans.Name'),
        type: Translator.trans('trans.Type'),
        size: Translator.trans('trans.Size'),
        dimensions: Translator.trans('trans.Dimensions'),
        duration: Translator.trans('trans.Duration'),
        crop: Translator.trans('trans.Crop'),
        rotate: Translator.trans('trans.Rotate'),
        sort: Translator.trans('trans.Sort'),
        download: Translator.trans('trans.Download'),
        remove: Translator.trans('trans.Remove'),
        drop: Translator.trans('trans.Drop the files here to Upload'),
        paste: '<div class="fileuploader-pending-loader"></div> '+ Translator.trans('trans.Pasting a file, click here to cancel') +'.',
        removeConfirmation: Translator.trans('trans.Are you sure you want to remove this file?'),
        errors: {
            filesLimit: Translator.trans('trans.Only ${limit} files are allowed to be uploaded'),
            filesType: Translator.trans('trans.Only ${extensions} files are allowed to be uploaded'),
            fileSize: Translator.trans('trans.${name} is too large! Please choose a file up to ${fileMaxSize}MB'),
            filesSizeAll: Translator.trans('trans.Files that you chose are too large! Please upload files up to ${maxSize} MB'),
            fileName: Translator.trans('trans.File with the name ${name} is already selected'),
            folderUpload: Translator.trans('trans.You are not allowed to upload folders')
        }
    }
});