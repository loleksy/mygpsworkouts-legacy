
var TcxImport = function(options){
    this.initState();
    this.setOptions(options);
    this.initDropzone();
};

TcxImport.prototype.initState = function(){
    this.handledFilesCount = 0;
    this.pendingFilesCount = 0;
}

TcxImport.prototype.setOptions = function(options) {
    if (!options.uploadUrl) {
        throw "no uploadUrl in options.";
    }
    this.options = {};
    this.options.uploadUrl = options.uploadUrl;
}

TcxImport.prototype.initDropzone = function() {
    /* init dropzone */

    // Get the template HTML and remove it from the doumenthe template HTML and remove it from the doument
    var previewNode = document.querySelector("#dropzone-template");
    previewNode.id = "";
    var previewTemplate = previewNode.parentNode.innerHTML;
    previewNode.parentNode.removeChild(previewNode);

    this.dropzone = new Dropzone(document.body, { // Make the whole body a dropzone
        url: this.options.uploadUrl, // Set the url
        parallelUploads: 1,
        previewTemplate: previewTemplate,
        autoQueue: false, // Make sure the files aren't queued until manually added
        previewsContainer: "#previews", // Define the container to display the previews
        clickable: ".fileinput-button" // Define the element that should be used as click trigger to select files.
    });

    /* setup events */
    var that = this;

    // Setup the buttons for all transfers
    // The "add files" button doesn't need to be setup because the config
    // `clickable` has already been specified.
    document.querySelector("#actions .start").onclick = function(e) {
        that.onStartClicked(e);
    };

    this.dropzone.on('addedfile', function(){
        that.onDropzoneFileAdded();
    });

    this.dropzone.on('removedfile', function(){
        that.onDropzoneFileRemoved();
    });

    this.dropzone.on("complete", function(){
        that.onDropzoneComplete();
    });

    // Update the total progress bar
    this.dropzone.on("totaluploadprogress", function(progress) {
        that.onDropzoneTotalUploadProgress();
    });

    this.dropzone.on("sending", function(file) {
        that.onDropzoneSending(file);
    });

    this.dropzone.on("queuecomplete", function() {
        that.onDropzoneQueueComplete();
    });

    this.dropzone.on("success", function( file, result ) {
        that.onDropzoneSuccess(file, result);
    });

    // attach callback to the 'error' event
    this.dropzone.on("error", function( file, errorMessage, xhr ) {
        that.onDropzoneError(file, errorMessage, xhr);
    });

}

TcxImport.prototype.checkStartButtonVisibility = function(){
    if(this.pendingFilesCount){
        $('#actions .start').show();
    }
    else{
        $('#actions .start').hide();
    }
}


TcxImport.prototype.onStartClicked = function(e){
    $('#action-buttons').hide();
    //remove delete buttons
    for(var i in this.dropzone.getAcceptedFiles()){
        this.dropzone.getAcceptedFiles()[i].previewElement.querySelector(".delete").remove();
    }

    //start upload
    this.dropzone.enqueueFiles(this.dropzone.getFilesWithStatus(Dropzone.ADDED));
}

TcxImport.prototype.onDropzoneFileAdded = function(){
    this.pendingFilesCount+=1;
    this.checkStartButtonVisibility();
}

TcxImport.prototype.onDropzoneFileRemoved = function(){
    this.pendingFilesCount-=1;
    this.checkStartButtonVisibility();
}

TcxImport.prototype.onDropzoneComplete = function(){
    this.handledFilesCount+=1;
}

TcxImport.prototype.onDropzoneTotalUploadProgress = function(){
    //progress is buggy and returns wrong values
    var totalFilesCount = this.dropzone.getAcceptedFiles().length;
    if(totalFilesCount){
        var progress = Math.round((this.handledFilesCount/totalFilesCount)*100);
        document.querySelector("#total-progress .progress-bar").style.width = progress + "%";
    }
}

TcxImport.prototype.onDropzoneSending = function(file){
    // Show the total progress bar when upload starts
    $('#current-file').text(file.name);
    $(file.previewElement.querySelector(".spinner")).show();
}

TcxImport.prototype.onDropzoneQueueComplete = function(){
    $(".progress-bar").hide();
    $("#total-progress").hide();
    $('#current-file').hide();
    $('#upload-completed-actions').show();
}

TcxImport.prototype.onDropzoneSuccess = function(file, result){
    // the file parameter is https://developer.mozilla.org/en-US/docs/DOM/File
    // the result parameter is the result from the server
    for(var i in result){
        var labelType = result[i].success?'label-success':'label-danger';
        $label = $('<span />').addClass('label');
        if(result[i].success){
            $label.addClass('label-success');
        }
        else{
            $label.addClass('label-danger');
        }
        if(result[i].datetime){
            $label.html(result[i].datetime + ': ' + result[i].message);
        }
        else{
            $label.html(result[i].message);
        }

        $(file.previewElement.querySelector(".server-response-container")).append($label);
        $(file.previewElement.querySelector(".spinner")).hide();
    }
}

TcxImport.prototype.onDropzoneError = function(file, errorMessage, xhr){
    // if the xhr parameter exists, it means the error was server-side
    if (xhr && xhr.status === 401) {
        //session expired, redirect whole page
        window.location.reload();
        return;
    }
    $(file.previewElement.querySelector(".server-response-container")).append('<span class="label label-danger">error</span>');
    $(file.previewElement.querySelector(".spinner")).hide();
}