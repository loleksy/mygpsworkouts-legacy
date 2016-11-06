var myGpsWorkouts = myGpsWorkouts || {};
myGpsWorkouts.core = myGpsWorkouts.core || {};

myGpsWorkouts.core.Storage = function(){
    this.storageBackend = localforage.createInstance({
        name: "'myGpsWorkoutsDb"
    });
};

myGpsWorkouts.core.Storage.prototype.getTrackpoints  = function(workoutId, callback){
    this.storageBackend.getItem('trackpoints_' + String(workoutId)).then(function(value) {
        callback(value);
    });
};

myGpsWorkouts.core.Storage.prototype.setTrackpoints  = function(workoutId, data, callback){
    this.storageBackend.setItem('trackpoints_' + String(workoutId), data).then(function () {
        callback();
    })
};