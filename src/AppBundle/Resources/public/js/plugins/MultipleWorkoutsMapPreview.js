var myGpsWorkouts = myGpsWorkouts || {};
myGpsWorkouts.plugins = myGpsWorkouts.plugins || {};



myGpsWorkouts.plugins.MultipleWorkoutsMapPreview = function(options){
    this.initState();
};

myGpsWorkouts.plugins.MultipleWorkoutsMapPreview.prototype.initState = function(){
    this.selectors = {};
    this.selectors.map  = '#allWorkoutsMapCanvas';
    this.mapData = {};
};

myGpsWorkouts.plugins.MultipleWorkoutsMapPreview.prototype.render = function(){
    this.initMap();
};

myGpsWorkouts.plugins.MultipleWorkoutsMapPreview.prototype.initMap = function(){
    var mapOptions = {
        center: { lat: 0, lng: 0},
        zoom: 2
    };
    this.mapData.map = new google.maps.Map(document.querySelector(this.selectors.map), mapOptions);
    this.fullScreenMapToggle = new myGpsWorkouts.core.FullScreenMapToggle(this.mapData.map);
    this.fullScreenMapToggle.addResizeButton();
};






