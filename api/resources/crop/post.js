const AWS = require('aws-sdk');
const request = require('request');
const gm = require('gm').subClass({ imageMagick: true });
const smartcrop = require('smartcrop-gm');
const dateFormat = require('dateformat');
const adresa = require("url");
const path = require("path");
const crypto = require('crypto');
const async = require("async");

const spacesEndpoint = new AWS.Endpoint('https://s3.ungheni.today');
const s3 = new AWS.S3({
    endpoint: spacesEndpoint,
    accessKeyId: '792XVHXUWKKJ8CQW1U0Y',
    secretAccessKey: 'gIQMdhG2IxSLfafySZgOyMYL42OlL9YWHIya3+Lz',
    s3ForcePathStyle: true,
    signatureVersion: 'v4'
});

const day = dateFormat(new Date(), "yyyy/mm/dd/");

/*
"wide-extralarge": { 
    href: "",
    width: "1250",
    height: "703",
   }, 
  "wide-large": { 
    href: "",
    width: "800",
    height: "450",
   },
   "wide-medium": { 
    href: "",
    width: "336",
    height: "189",
   },
   "wide-small": { 
    href: "",
    width: "100",
    height: "56",
   },
   "square-medium": { 
    href: "",
    width: "450",
    height: "450",
   },
   "square-small": { 
    href: "",
    width: "100",
    height: "100",
   }
   */
var outputRenditions = {};

function applySmartCrop(src, name, rendname, width, height) {
  $addCallback();
  request(src, { encoding: null }, function(error, response, body) {
    if (error) return console.error(error);
    smartcrop.crop(body, { width: width, height: height }).then(function(result) {
      var crop = result.topCrop;
      gm(body)
        .crop(crop.width, crop.height, crop.x, crop.y)
        .resize(width, height)
        .toBuffer(function (err, buffer) {
          var params = {
              Body: buffer,
              Bucket: "ungheni",
              Key: "images/"+day+name,
          };
          s3.upload(params, function(err, data) {
              if (err) {
                  //console.log(err, err.stack);
              } else {
                  //console.log(data);
                  outputRenditions[rendname] = {href: data.Location, width: width, height: height};
                  //return outputRenditions;
                  return $finishCallback();
              }
          });
        });
    });
  });
}

dpd.settings.get({$fields: {renditions: 1}}, function(results, error) {
    renditions = results[0].renditions;
    var parsed = adresa.parse(body.url);
    var remotename = path.basename(parsed.pathname);
    var ext = path.extname(remotename);
    
    for (var i = 0, len = renditions.length; i < len; i++) {
        var filebase = crypto.createHash('md5').update(renditions[i].name + remotename).digest('hex');
        var filename = filebase + ext;
        var rezultat = applySmartCrop(body.url, filename, renditions[i].name, renditions[i].width, renditions[i].height);
    }
    setResult(outputRenditions);
});