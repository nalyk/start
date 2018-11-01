var news = this;

setOnGetVariables();

function setOnGetVariables(){
    setFeaturedImage(news.id)
    .then(setFeaturedVideo);
/*    
    .then(second)
    .then(third);
*/
}

function setFeaturedImage(a){
    return new Promise((resolve, reject) => {
        if (typeof news.associatedMedia.featuredImage.id !== 'undefined' && news.associatedMedia.featuredImage.id !== null) {
            dpd.media.get({id: news.associatedMedia.featuredImage.id}, function(image) {
                news.associatedMedia.featuredImage = image;
            });
            console.log(news.id);
        }
        resolve(a);  
    });
}

function setFeaturedVideo(b){
    return new Promise((resolve, reject) => {
        if (typeof news.associatedMedia.featuredVideo.id !== 'undefined' && news.associatedMedia.featuredVideo.id !== null) {
            dpd.media.get({id: news.associatedMedia.featuredVideo.id}, function(video) {
                news.associatedMedia.featuredVideo = video;
            });
            console.log(news.id);
        }
        resolve(b);  
    });
}