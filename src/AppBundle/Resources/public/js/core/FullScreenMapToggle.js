var myGpsWorkouts = myGpsWorkouts || {};
myGpsWorkouts.core = myGpsWorkouts.core || {};

myGpsWorkouts.core.FullScreenMapToggle = function(map){
    this.map = map;
    this.isFullScreen = false;
};


myGpsWorkouts.core.FullScreenMapToggle.prototype.addResizeButton = function(){
    var controlDiv = document.createElement('div');
    controlDiv.style.paddingTop = '4px';

    // Set CSS for the control interior
    var resizeButton = document.createElement('button');
    resizeButton.className = "btn btn-default btn-xs";


    resizeButton.innerHTML = '<span class="glyphicon glyphicon-resize-full"></span>';
    controlDiv.appendChild(resizeButton );

    // Setup the click event listeners: simply set the map to
    var that = this;
    google.maps.event.addDomListener(resizeButton, 'click', function(e) {
        that.onResizeButtonClicked(e);
    });

    controlDiv.index = 1;
    this.map.controls[google.maps.ControlPosition.TOP_RIGHT].push(controlDiv);
};

myGpsWorkouts.core.FullScreenMapToggle.prototype.onResizeButtonClicked = function(event){
    if(!this.isFullScreen){
        $(event.currentTarget).find('span').removeClass('glyphicon-resize-full').addClass('glyphicon-resize-small');
        $(this.map.getDiv()).addClass('fullScreen');
    }
    else{
        $(event.currentTarget).find('span').removeClass('glyphicon-resize-small').addClass('glyphicon-resize-full');
        $(this.map.getDiv()).removeClass('fullScreen');
    }
    google.maps.event.trigger(this.map, 'resize');
    this.isFullScreen = !this.isFullScreen;
    this.onMapResized();
};

myGpsWorkouts.core.FullScreenMapToggle.prototype.onMapResized = function(){};


