var myGpsWorkouts = myGpsWorkouts || {};
myGpsWorkouts.plugins = myGpsWorkouts.plugins || {};



myGpsWorkouts.plugins.WorkoutMapPreview= function(data, options){
    this.initState();
    this.setOptions(options);
    this.setData(data);
};


myGpsWorkouts.plugins.WorkoutMapPreview.prototype.initState = function(){
    this.selectors = {};
    this.selectors.map  = '#workoutMapCanvas';
    this.mapData = {};
    this.mapData.markers = {};
    this.mapData.markers.checkpoints = new Array();
    this.mapData.isFullScreen = false;
    this.imageGenerator = new myGpsWorkouts.plugins.WorkoutMarkerImageGenerator();
    this.mapData.markerIconSettings = {
        size: new google.maps.Size(24, 24),
        origin: new google.maps.Point(0,0),
        anchor: new google.maps.Point(12, 12)

    }

};

myGpsWorkouts.plugins.WorkoutMapPreview.prototype.setOptions = function(options){
    this.options = {};
    this.options.color = options.color || '#ff0000';
};

myGpsWorkouts.plugins.WorkoutMapPreview.prototype.setData = function(data) {
    this.data = data;
    var latLngs = new Array();
    var bounds = new google.maps.LatLngBounds();
    for(var i in this.data){
        var latlng = new google.maps.LatLng(this.getRawDataRowValue(i, 'lat'), this.getRawDataRowValue(i, 'lng'));
        latLngs.push(latlng);
        bounds.extend(latlng);
    }
    this.mapData.polyLineLatLngs = latLngs;
    this.mapData.bounds = bounds;
};

myGpsWorkouts.plugins.WorkoutMapPreview.prototype.render = function(){
    this.initMap();
    this.addResizeButton();
    this.renderPolyLine();
    this.fitMapToPolyLineBounds();
    this.renderStartMarker();
    this.renderCheckPointMarkers();
    this.renderEndMarker();
    this.setMapEvents();
};

myGpsWorkouts.plugins.WorkoutMapPreview.prototype.initMap = function(){
    var mapOptions = {
        center: { lat: 0, lng: 0},
        zoom: 2
    };
    this.mapData.map = new google.maps.Map(document.querySelector(this.selectors.map), mapOptions);
};

myGpsWorkouts.plugins.WorkoutMapPreview.prototype.addResizeButton = function(){
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
    this.mapData.map.controls[google.maps.ControlPosition.TOP_RIGHT].push(controlDiv);
};

myGpsWorkouts.plugins.WorkoutMapPreview.prototype.onResizeButtonClicked = function(event){
    if(!this.mapData.isFullScreen){
        $(event.currentTarget).find('span').removeClass('glyphicon-resize-full').addClass('glyphicon-resize-small');
        $(this.selectors.map).addClass('fullScreen');
    }
    else{
        $(event.currentTarget).find('span').removeClass('glyphicon-resize-small').addClass('glyphicon-resize-full');
        $(this.selectors.map).removeClass('fullScreen');
    }
    google.maps.event.trigger(this.mapData.map, 'resize');
    this.fitMapToPolyLineBounds();
    this.mapData.isFullScreen = !this.mapData.isFullScreen;
};

myGpsWorkouts.plugins.WorkoutMapPreview.prototype.renderPolyLine = function(){
    this.mapData.polyLine = new google.maps.Polyline({
        path: this.mapData.polyLineLatLngs,
        geodesic: true,
        strokeColor:  this.options.color,
        strokeOpacity: 1.0,
        strokeWeight: 2,
        map:this.mapData.map
    });
};

myGpsWorkouts.plugins.WorkoutMapPreview.prototype.fitMapToPolyLineBounds = function(){
    this.mapData.map.fitBounds(this.mapData.bounds);
}

myGpsWorkouts.plugins.WorkoutMapPreview.prototype.renderCheckPointMarkers = function(){
    if(!this.mapData.polyLineLatLngs || this.mapData.polyLineLatLngs.length < 2){
        return;
    }
    var distance = 1000;
    var i=1;
    var length = google.maps.geometry.spherical.computeLength(this.mapData.polyLineLatLngs);
    var remainingDist = length;
    var markersGap = this.calcOptimalCheckPointMarkersGap();
    while (remainingDist > 0)
    {
        var marker = new google.maps.Marker({
            position:myGpsWorkouts.utils.geo.getPointAtDistance(this.mapData.polyLine, 1000*i),
            title: String(i),
            icon: {
                url: this.imageGenerator.generateNumberImage(this.options.color, i).getCanvas().toDataURL(),
                size: this.mapData.markerIconSettings.size,
                origin: this.mapData.markerIconSettings.origin,
                anchor: this.mapData.markerIconSettings.anchor
            }
        });
        if(markersGap ||(i % markersGap === 0)){
            marker.setMap(this.mapData.map);
        }
        this.mapData.markers.checkpoints.push(marker);
        remainingDist -= distance;
        i++;
    }
    this.mapData.renderedCheckPointMarkersGap =  markersGap;
}

myGpsWorkouts.plugins.WorkoutMapPreview.prototype.renderStartMarker = function(){
    if(!this.mapData.polyLineLatLngs || this.mapData.polyLineLatLngs.length < 2){
        return;
    }
    var marker = new google.maps.Marker({
        position:this.mapData.polyLineLatLngs[0],
        map:this.mapData.map,
        title: 'start',
        icon: {
            url:this.imageGenerator.generateStartImage(this.options.color).getCanvas().toDataURL(),
            size: this.mapData.markerIconSettings.size,
            origin: this.mapData.markerIconSettings.origin,
            anchor: this.mapData.markerIconSettings.anchor
        }
    });
    this.mapData.markers.checkpoints.push(marker);
};

myGpsWorkouts.plugins.WorkoutMapPreview.prototype.renderEndMarker = function(){
    if(!this.mapData.polyLineLatLngs || this.mapData.polyLineLatLngs.length < 2){
        return;
    }
    var marker = new google.maps.Marker({
        position:this.mapData.polyLineLatLngs[this.mapData.polyLineLatLngs.length-1],
        map:this.mapData.map,
        title: 'fin',
        icon: {
            url: this.imageGenerator.generateEndImage(this.options.color).getCanvas().toDataURL(),
            size: this.mapData.markerIconSettings.size,
            origin: this.mapData.markerIconSettings.origin,
            anchor: this.mapData.markerIconSettings.anchor
        }
    });
    this.mapData.markers.checkpoints.push(marker);
};

myGpsWorkouts.plugins.WorkoutMapPreview.prototype.setMapEvents = function(){
    var that = this;
    google.maps.event.addListener(this.mapData.map, 'zoom_changed', function() {
       that.onZoomChanged();
    });
};

myGpsWorkouts.plugins.WorkoutMapPreview.prototype.calcOptimalCheckPointMarkersGap = function(){
    var zoom = this.mapData.map.getZoom();
    if(zoom > 13){
        return 1;
    }
    if(zoom > 8){
        return 10;
    }
    return 0;
}

myGpsWorkouts.plugins.WorkoutMapPreview.prototype.onZoomChanged = function(){
    var markerGap = this.calcOptimalCheckPointMarkersGap();
    if(markerGap !== this.mapData.renderedCheckPointMarkersGap){
        for(var i=0; i< this.mapData.markers.checkpoints.length-1; i++){
            if(i%markerGap !== 0){
                this.mapData.markers.checkpoints[i].setMap(null);
            }
            else{
                this.mapData.markers.checkpoints[i].setMap(this.mapData.map);
            }
        }
    }
    this.mapData.renderedCheckPointMarkersGap = markerGap;
}


myGpsWorkouts.plugins.WorkoutMapPreview.prototype.getRawDataRowValue = function(index, key){
    switch(key){
        case 'timestamp':
            return this.data[index][0];
        case 'lat':
            return this.data[index][1];
        case 'lng':
            return this.data[index][2];
        case 'altitude_meters':
            return this.data[index][3];
        case 'heart_rate_bpm':
            return this.data[index][4];
        default:
            throw "Unknown key"
    }
};


