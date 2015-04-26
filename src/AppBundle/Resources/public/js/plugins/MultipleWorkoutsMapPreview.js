var myGpsWorkouts = myGpsWorkouts || {};
myGpsWorkouts.plugins = myGpsWorkouts.plugins || {};



myGpsWorkouts.plugins.MultipleWorkoutsMapPreview = function(options){
    this.initState();
    this.setOptions(options);
};

myGpsWorkouts.plugins.MultipleWorkoutsMapPreview.prototype.setOptions = function(options){
    this.options = options;
    if(!this.options.ajaxWorkoutsUrl){
        throw "ajaxWorkoutsUrl option not provided!";
    }
    if(!this.options.workoutPreviewUrlTemplate){
        throw "workoutPreviewUrlTemplate option not provided!";
    }
};

myGpsWorkouts.plugins.MultipleWorkoutsMapPreview.prototype.initState = function(){
    this.selectors = {};
    this.selectors.map  = '#allWorkoutsMapCanvas';
    this.mapData = {};
    this.sportColors = {};
    var that = this;
    $("#mapSportsSelect > option").each(function() {
        var $option = $(this);
        that.sportColors[$option.val()] = $option.attr('data-color');
    });
    this.storage = new myGpsWorkouts.core.Storage();

};

myGpsWorkouts.plugins.MultipleWorkoutsMapPreview.prototype.start = function(){
    this.initMap();
    this.registerEvents();
    this.onApplyButtonClicked();
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


myGpsWorkouts.plugins.MultipleWorkoutsMapPreview.prototype.registerEvents = function(){
    var that = this;
    $("#applyMapChangesButton").click( function(e) { that.onApplyButtonClicked(); });
};


myGpsWorkouts.plugins.MultipleWorkoutsMapPreview.prototype.onApplyButtonClicked = function(){
    var startTs = new Date($('#mapStartDateInput').val()).getTime()/1000;
    var endTs = new Date($('#mapEndDateInput').val()).getTime()/1000;
    var sportIds = [];
    $('#mapSportsSelect :selected').each(function(i, selected){
        sportIds.push($(selected).val());
    });
    this.fetchWorkouts(startTs, endTs, sportIds);
};

//fetch workouts list
myGpsWorkouts.plugins.MultipleWorkoutsMapPreview.prototype.fetchWorkouts = function(startTs, endTs, sportIds){
    var params = {
        'start_ts': startTs,
        'end_ts': endTs,
        'sport_ids': sportIds.join(',')
    };
    var url = this.options.ajaxWorkoutsUrl + '?' + $.param(params);
    var that = this;
    this.cleanMapData();
    $.get(url, function(response){
        that.renderWorkouts(response);
    });
};

myGpsWorkouts.plugins.MultipleWorkoutsMapPreview.prototype.cleanMapData = function(){
    if(this.mapData.polylines){
        for(var i in this.mapData.polylines){
            this.mapData.polylines[i].setMap(null);
        }
    }
    this.mapData.polylines = [];
};

//for each item, fetch trackpoints and render
myGpsWorkouts.plugins.MultipleWorkoutsMapPreview.prototype.renderWorkouts = function(workouts, totalItemsCount, handledItemsCount){
   this.updateProgressBar(totalItemsCount, handledItemsCount);
   if(!workouts.length){
       return;
   }
   if(!totalItemsCount){
       totalItemsCount = workouts.length;
   }
   if(!handledItemsCount){
       handledItemsCount = 0;
   }
   var that = this;
   var currentItem = workouts.shift();

   this.fetchTrackpoints(currentItem.id, function(trackpoints){
       currentItem.trackpoints = trackpoints;
       that.renderWorkout(currentItem, handledItemsCount === 0); //fit to bounds for first (most recent) result
       that.renderWorkouts(workouts, totalItemsCount, handledItemsCount + 1); //handle items that left
   });

};

//fetches trackpoints from large local storage, if not available, fetches data from WS, finally calls callback
myGpsWorkouts.plugins.MultipleWorkoutsMapPreview.prototype.fetchTrackpoints = function(workoutId, callback){
    var that = this;
    this.storage.getTrackpoints(workoutId, function(trackpoints){
        if(trackpoints){
            callback(trackpoints);
        }
        else{
            var url = that.options.ajaxWorkoutsUrl + '/' + workoutId + '/trackpoint';
            $.get(url, function(response){
                console.log('fetched');
                that.storage.setTrackpoints(workoutId, response, function(){});
                callback(response);
            });
        }
    });
};

myGpsWorkouts.plugins.MultipleWorkoutsMapPreview.prototype.updateProgressBar = function(totalItemsCount, handledItemsCount){
    var percents = Math.round((handledItemsCount/totalItemsCount)*100);
    $('#mapDataProgressBar').css('width', String(percents)+'%').attr('aria-valuenow', String(percents));
};

myGpsWorkouts.plugins.MultipleWorkoutsMapPreview.prototype.renderWorkout = function(workout, fitBounds){
    var latLngs = new Array();
    for(var i in workout.trackpoints){
        latLngs.push(new google.maps.LatLng(workout.trackpoints[i][1], workout.trackpoints[i][2]));
    }
    var polyline = new google.maps.Polyline({
        path: latLngs,
        geodesic: true,
        strokeColor: this.sportColors[workout.sport.id],
        strokeOpacity: 1.0,
        strokeWeight: 2
    });
    polyline.setMap(this.mapData.map);
    this.mapData.polylines.push(polyline);
    this.setPolylineInfoWindow(polyline, workout);
    if(fitBounds){
        var bounds = new google.maps.LatLngBounds();
        for(var i=0; i< latLngs.length; i++){
            bounds.extend(latLngs[i]);
        }
        this.mapData.map.fitBounds(bounds);
    }
};

myGpsWorkouts.plugins.MultipleWorkoutsMapPreview.prototype.setPolylineInfoWindow = function(polyline, workout){
    var that = this;
    google.maps.event.addListener(polyline, 'click', function(event) {
        if (that.mapData.previousInfoWindow) {
            that.mapData.previousInfoWindow.close();
        }
        var displayDate = myGpsWorkouts.utils.formatDate(new Date(workout.startTimestamp*1000));
        var windowContent = '<strong> ' + displayDate + '</strong>';
        var workoutUrl = that.getWorkoutPreviewUrl(workout.id);
        windowContent+=' <a href="' + workoutUrl + '"class="btn btn-default btn-xs"><span class="glyphicon glyphicon-new-window"></span></a>';
        var infowindow = new google.maps.InfoWindow({
            content: windowContent,
            position: event.latLng
        });
        infowindow.open(that.mapData.map);
        that.mapData.previousInfoWindow = infowindow;
    });
};

myGpsWorkouts.plugins.MultipleWorkoutsMapPreview.prototype.getWorkoutPreviewUrl = function(workoutId){
    return this.options.workoutPreviewUrlTemplate.replace('__ID__', workoutId);
};





