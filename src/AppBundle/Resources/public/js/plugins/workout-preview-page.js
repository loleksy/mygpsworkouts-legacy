
var WorkoutMapPreview = function(data, options){
    this.initState();
    this.setOptions(options);
    this.setData(data);
};


WorkoutMapPreview.prototype.initState = function(){
    this.selectors = {};
    this.selectors.map  = '#workoutMapCanvas';
    this.mapData = {};
};

WorkoutMapPreview.prototype.setOptions = function(options){
    this.options = {};
    this.options.color = options.color || '#ff0000';
};

WorkoutMapPreview.prototype.setData = function(data) {
    this.data = data;
};

WorkoutMapPreview.prototype.render = function(){
    this.initMap();
    this.addResizeButton();
    this.renderPolyLine();
};

WorkoutMapPreview.prototype.initMap = function(){
    var mapOptions = {
        center: { lat: 0, lng: 0},
        zoom: 2
    };
    this.mapData.map = new google.maps.Map(document.querySelector(this.selectors.map), mapOptions);
};

WorkoutMapPreview.prototype.addResizeButton = function(){
    var controlDiv = document.createElement('div');
    controlDiv.style.paddingTop = '4px';

    // Set CSS for the control interior
    var resizeButton = document.createElement('button');
    resizeButton.className = "btn btn-default btn-xs";


    resizeButton.innerHTML = '<span class="glyphicon glyphicon-resize-full"></span>';
    controlDiv.appendChild(resizeButton );

    // Setup the click event listeners: simply set the map to
    // Chicago
    var that = this;
    google.maps.event.addDomListener(resizeButton, 'click', function() {
        that.onResizeButtonClicked();
    });

    controlDiv.index = 1;
    this.mapData.map.controls[google.maps.ControlPosition.TOP_RIGHT].push(controlDiv);
};

WorkoutMapPreview.prototype.onResizeButtonClicked = function(){
   alert('to do');
};

WorkoutMapPreview.prototype.renderPolyLine = function(){
    var latLngs = new Array();
    var bounds = new google.maps.LatLngBounds();
    for(var i in this.data){
        var latlng = new google.maps.LatLng(this.getRawDataRowValue(i, 'lat'), this.getRawDataRowValue(i, 'lng'));
        latLngs.push(latlng);
        bounds.extend(latlng);
    }
    this.mapData.polyLineLatLngs = latLngs;
    this.mapData.polyLine = new google.maps.Polyline({
        path: this.mapData.polyLineLatLngs,
        geodesic: true,
        strokeColor:  this.options.color,
        strokeOpacity: 1.0,
        strokeWeight: 2,
        map:this.mapData.map
    });
    this.mapData.map.fitBounds(bounds);
};

WorkoutMapPreview.prototype.getRawDataRowValue = function(index, key){
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


