$('#policeLogCopyToClipboard').click(function() {
    copyTextToClipboard(app.getJsonDataCached()['policeLogText']);
});