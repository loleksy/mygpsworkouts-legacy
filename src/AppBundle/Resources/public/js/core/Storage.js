var myGpsWorkouts = myGpsWorkouts || {};
myGpsWorkouts.core = myGpsWorkouts.core || {};

myGpsWorkouts.core.Storage = function(){
    var desiredCapacity = 256 * 1024 * 1024;
    this.storageBackend = new LargeLocalStorage({size: desiredCapacity, name: 'myGpsWorkoutsDb'});
    this.storageInitialized = false;
    var that = this;
    this.storageBackend.initialized.then(function() {
        if(that.storageBackend.getCapacity() > -1){
            that.storageInitialized = true;
        }
    });
};

myGpsWorkouts.core.Storage.prototype.getTrackpoints  = function(workoutId, callback){
    if(!this.storageInitialized){
        callback(null);
    }
    this.storageBackend.getContents('trackpoints_' + String(workoutId)).then(function(content) {
        if(content){
            callback(JSON.parse(content));
        }
        else{
            callback(null);
        }
    });
};

myGpsWorkouts.core.Storage.prototype.setTrackpoints  = function(workoutId, data, callback){
    if(!this.storageInitialized){
        callback(null);
    }
    this.storageBackend.setContents('trackpoints_' + String(workoutId), JSON.stringify(data)).then(function() {
        callback();
    });
};